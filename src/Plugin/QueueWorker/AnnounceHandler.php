<?php

namespace Drupal\blockchain\Plugin\QueueWorker;


use Drupal\blockchain\Service\BlockchainQueueServiceInterface;
use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\blockchain\Utils\BlockchainRequest;
use Drupal\blockchain\Utils\BlockchainResponseInterface;
use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\Core\Queue\SuspendQueueException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Processes announce handling.
 *
 * @QueueWorker(
 * id = "announce_queue",
 * title = @Translation("Announce queue handler."),
 * )
 */
class AnnounceHandler extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  const LOGGER_CHANNEL = 'announce_handler';

  /**
   * Logger service.
   *
   * @var LoggerChannelFactory
   */
  protected $loggerFactory;

  /**
   * Blockchain service.
   *
   * @var BlockchainServiceInterface
   */
  protected $blockchainService;

  /**
   * Constructs a ImporterQueue worker.
   *
   * @param array $configuration
   *   Configuration array.
   * @param string $plugin_id
   *   Plugin id.
   * @param mixed $plugin_definition
   *   Plugin definition.
   * @param LoggerChannelFactory $loggerFactory
   *   Logger factory.
   * @param BlockchainServiceInterface $blockchainService
   *   Blockchain service.
   */
  public function __construct(array $configuration,
                              $plugin_id,
                              $plugin_definition,
                              LoggerChannelFactory $loggerFactory,
                              BlockchainServiceInterface $blockchainService) {

    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->loggerFactory = $loggerFactory;
    $this->blockchainService = $blockchainService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array
  $configuration, $plugin_id, $plugin_definition) {

    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('logger.factory'),
      $container->get('blockchain.service')
    );
  }

  /**
   * {@inheritdoc}
   *
   * TODO move to separate service here is not place for all this (collisions).
   */
  public function processItem($data) {

    $announceData = property_exists($data, BlockchainQueueServiceInterface::ANNOUNCE_QUEUE_ITEM) ?
      $data->{BlockchainQueueServiceInterface::ANNOUNCE_QUEUE_ITEM} : NULL;
    if (!$announceData) {
      throw new \Exception('Missing announce data.');
    }
    if (!($blockchainRequest = BlockchainRequest::wakeup($announceData))) {
      throw new \Exception('Invalid announce queue data.');
    }
    $blockchainNode = $this->blockchainService->getNodeService()->loadBySelfAndType(
      $blockchainRequest->getSelfParam(), $blockchainRequest->getTypeParam());
    if (!($blockchainNode)) {
      throw new \Exception('Invalid announce request data.');
    }
    $endPoint = $blockchainNode->getEndPoint();
    // Plan to fetch to cache if are conflicts...
    if ($this->blockchainService->getLockerService()->lockAnnounce()) {
      try {
        $result = $this->blockchainService->getApiService()
          ->executeFetch($endPoint, $this->blockchainService->getStorageService()->getLastBlock());
        if ($this->blocksCanBePulled($result)) {
          $neededBlocks = $result->getCountParam();
          $addedBlocks = 0;
          $fetchLimit = 5; // --->>> Setting for blocks fetching count.
          while ($neededBlocks > $addedBlocks) {
            $lastBlock = $this->blockchainService->getStorageService()->getLastBlock();
            $result = $this->blockchainService->getApiService()
              ->executePull($endPoint, $lastBlock, $fetchLimit);
            if ($result->hasBlocksParam()) {
              foreach ($result->getBlocksParam() as $item) {
                $block = $this->blockchainService->getStorageService()->createFromArray($item);
                if ($this->blockchainService
                  ->getValidatorService()
                  ->blockIsValid($block, $lastBlock)) {
                  $block->save();
                } else {
                  throw new \Exception('Not valid block detected while pull.');
                }
              }
            }
            $addedBlocks += $fetchLimit;
          }
          // Search and cache blocks only with pending blocks.
        } elseif ($result->hasCountParam() && $result->getCountParam() > 0) {
          // We are in conflict situation, so we need to compare counts.
          $blockCount = $this->blockchainService->getStorageService()->getBlockCount();
          if ($result->getCountParam() > $blockCount) {
            // We are sure remote blockchain has priority.
            // Check if we have generic same.
            $result = $this->blockchainService->getApiService()
              ->executeFetch($endPoint, $this->blockchainService->getStorageService()->getFirstBlock());
            // This means generic matches, else this is fully different blockchain,
            // thus we take no action, sync possible with empty storage.
            if ($this->blocksCanBePulled($result)) {
              // We see that generic matches, find last match block.
              $i = 0;
              $blockSearchStep = 2; // ---> This variable to config.
              // Ensure step is not more than count itself.
              $blockSearchStep = ($blockCount < $blockSearchStep)? $blockCount : $blockSearchStep;
              do {
                $i += $blockSearchStep;
                $offset = ($blockCount - $i) < 0 ? 0 : $blockCount - $i;
                $blockchainBlocks = $this->blockchainService->getStorageService()->getBlocks($offset, $blockSearchStep);
                $result = $this->blockchainService->getApiService()
                  ->executeFetch($endPoint, reset($blockchainBlocks));
              } while (!$this->blocksCanBePulled($result));
              // At this point we have array, where first block is valid.
              // Lets find index of first not valid block.
              $validIndex = 0;
              for ($i = 1; $i < count($blockchainBlocks); $i++) {
                $result = $this->blockchainService->getApiService()
                  ->executeFetch($endPoint, $blockchainBlocks[$i]);
                if ($this->blocksCanBePulled($result)) {
                  $validIndex = $i;
                }
                else {
                  break;
                }
              }
              // We found that!!!
              $validBlock = $blockchainBlocks[$validIndex];
              // Make sure tep store is clear.
              $this->blockchainService->getTempStoreService()->deleteAll();
              // Add this block as 'generic' to tempStorage.
              $this->blockchainService->getTempStoreService()->save($validBlock);
              $neededBlocks = $result->getCountParam();
              $addedBlocks = 0;
              $fetchLimit = 5; // --->>> Setting for blocks fetching count.
              while ($neededBlocks > $addedBlocks) {
                // Use tempStorage service here...
                $lastBlock = $this->blockchainService->getTempStoreService()->getLastBlock();
                $result = $this->blockchainService->getApiService()
                  ->executePull($endPoint, $lastBlock, $fetchLimit);
                if ($result->hasBlocksParam()) {
                  foreach ($result->getBlocksParam() as $item) {
                    $block = $this->blockchainService->getStorageService()->createFromArray($item);
                    if ($this->blockchainService
                      ->getValidatorService()
                      ->blockIsValid($block, $lastBlock)) {
                      $this->blockchainService->getTempStoreService()->save($block);
                    } else {
                      // Delete any blocks.
                      $this->blockchainService->getTempStoreService()->deleteAll();
                      throw new \Exception('Not valid block detected while pull.');
                    }
                  }
                }
                $addedBlocks += $fetchLimit;
              }
              // We should have valid blocks in cache collected.
              // Check again if we really must delete blocks from storage.
              $countToDelete = $this->blockchainService->getStorageService()->getBlocksCountFrom($validBlock);
              $countToAdd = $this->blockchainService->getTempStoreService()->getBlockCount();
              $nodeId = $this->blockchainService->getConfigService()->getCurrentConfig()->getNodeId();
              if ($countToAdd > $countToDelete) {
                $lastBlock = $this->blockchainService->getStorageService()->getLastBlock();
                while ($lastBlock && !($validBlock->equals($lastBlock))) {
                  if ($lastBlock->getAuthor() == $nodeId) {
                    $this->blockchainService->getQueueService()
                      ->addBlockItem($lastBlock->getData(), $lastBlock->getEntityTypeId());
                  }
                  $this->blockchainService->getStorageService()->pop();
                  $lastBlock = $this->blockchainService->getStorageService()->getLastBlock();
                }
              }
              // Move from cache to db.
              // To be faster get all...
              $blocks = $this->blockchainService->getTempStoreService()->getAll();
              // Shift first existing block.
              array_shift($blocks);
              foreach ($blocks as $blockchainBlock) {
                $this->blockchainService->getStorageService()->save($blockchainBlock);
              }
              // Clear temp store.
              $this->blockchainService->getTempStoreService()->deleteAll();
            }
          }
        }
      } finally {
        // Always release lock.
        $this->blockchainService->getLockerService()->releaseAnnounce();
      }
    }
    else {
      throw new SuspendQueueException('Announce handling locked');
    }
  }

  /**
   * Validator function.
   *
   * @param BlockchainResponseInterface $response
   *   Blockchain response.
   *
   * @return bool
   *   Test result.
   */
  protected function blocksCanBePulled(BlockchainResponseInterface $response) {

    // Can PULL if blocks found and there are pending blocks.
    if ($response->hasExistsParam() && $response->getExistsParam()
      && $response->hasCountParam() && $response->getCountParam() > 0) {

      return TRUE;
    }
    // Can PULL if any blocks in storage and there are pending blocks.
    if (!$this->blockchainService->getStorageService()->anyBlock() &&
      $response->hasCountParam() && $response->getCountParam() > 0) {

      return TRUE;
    }

    return FALSE;
  }

}
