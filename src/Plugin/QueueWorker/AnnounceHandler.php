<?php

namespace Drupal\blockchain\Plugin\QueueWorker;


use Drupal\blockchain\Service\BlockchainQueueServiceInterface;
use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\blockchain\Utils\BlockchainRequest;
use Drupal\blockchain\Utils\Util;
use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
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
    $blockchainNode = $this->blockchainService->getNodeService()->load($blockchainRequest->getSelfParam());
    if (!($blockchainNode)) {
      throw new \Exception('Invalid announce request data.');
    }
    $endPoint = $blockchainNode->getEndPoint();
    // Plan to fetch to cache if are conflicts...
    // LOCK for concurrent updates. FINALLY release.
    // Aim is one update to be consistent.
    $result = $this->blockchainService->getApiService()
      ->executeFetch($endPoint, $this->blockchainService->getStorageService()->getLastBlock());
    if ($result->hasExistsParam() && $result->getExistsParam() && $result->hasCountParam()) {
      $neededBlocks = $result->getCountParam();
      $addedBlocks = 0;
      $fetchLimit = 5; // --->>> Setting for blocks fetching count.
      while ($neededBlocks > $addedBlocks) {
        $result = $this->blockchainService->getApiService()
          ->executePull($endPoint, $this->blockchainService->getStorageService()->getLastBlock(), $fetchLimit);
        if ($result->hasBlocksParam()) {
          foreach ($result->getBlocksParam() as $item) {
            $block = $this->blockchainService->getStorageService()->createFromArray($item);
            if ($this
              ->blockchainService->getValidatorService()
              ->blockIsValid($block, $this->blockchainService->getStorageService()->getLastBlock())) {
              $block->save();
            }
            else {
              throw new \Exception('Not valid block detected.');
            }
          }
        }
        $addedBlocks += $fetchLimit;
      }
    }
    else {
      // Implement search and cache logic...
    }
    // Finally release lock ...
  }

  /**
   * Mining procedure.
   *
   * @param string $miningString
   *   Given value.
   *
   * @return string
   */
  protected function mine($miningString) {

    $nonce = 0;
    $result = Util::hash($miningString.$nonce);
    $validator = $this->blockchainService->getValidatorService();
    while (!$validator->hashIsValid($result)) {
      $nonce++;
      $result = Util::hash($miningString.$nonce);
    }

    return $nonce;
  }
}