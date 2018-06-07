<?php

namespace Drupal\blockchain_emulation\Service;


use Drupal\blockchain\Entity\BlockchainBlock;
use Drupal\blockchain\Entity\BlockchainBlockInterface;
use Drupal\blockchain\Plugin\BlockchainDataInterface;
use Drupal\blockchain\Plugin\BlockchainDataManager;
use Drupal\blockchain\Service\BlockchainConfigServiceInterface;
use Drupal\blockchain\Utils\Util;
use Drupal\Component\Utility\Random;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\State\StateInterface;

/**
 * Class BlockchainEmulationStorageService.
 *
 * @package Drupal\blockchain\Service
 */
class BlockchainEmulationStorageService implements BlockchainEmulationStorageServiceInterface {

  /**
   * Entity type manager.
   *
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Logger factory.
   *
   * @var LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * Blockchain config service.
   *
   * @var BlockchainConfigServiceInterface
   */
  protected $configService;

  /**
   * Blockchain data manager.
   *
   * @var BlockchainDataManager
   */
  protected $blockchainDataManager;

  /**
   * Drupal state.
   *
   * @var StateInterface
   */
  protected $state;

  /**
   * BlockchainStorageService constructor.
   *
   * @param EntityTypeManagerInterface $entityTypeManager
   *   Given service.
   * @param LoggerChannelFactoryInterface $loggerFactory
   *   Logger factory.
   * @param BlockchainConfigServiceInterface $blockchainSettingsService
   *   Blockchain config service.
   * @param BlockchainDataManager $blockchainDataManager
   *   Blockchain data manager.
   * @param StateInterface $state
   *   Drupal state.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager,
                              LoggerChannelFactoryInterface $loggerFactory,
                              BlockchainConfigServiceInterface $blockchainSettingsService,
                              BlockchainDataManager $blockchainDataManager,
                              StateInterface $state) {

    $this->entityTypeManager = $entityTypeManager;
    $this->loggerFactory = $loggerFactory;
    $this->configService = $blockchainSettingsService;
    $this->blockchainDataManager = $blockchainDataManager;
    $this->state = $state;
  }

  /**
   * {@inheritdoc}
   */
  public function getLogger() {

    return $this->loggerFactory->get(static::LOGGER_CHANNEL);
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockStorage() {

    return $this->state->get(static::STORAGE_NAMESPACE,  []);
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockCount() {

    return count($this->getBlockStorage());
  }

  /**
   * {@inheritdoc}
   */
  public function getLastBlock() {

    if ($count = $this->getBlockCount()) {

      return $this->getBlockStorage()[$count-1];
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function anyBlock() {

    return (bool) $this->getBlockCount();
  }

  /**
   * {@inheritdoc}
   */
  public function getGenericBlock() {

    $rand = new Random();
    $block = BlockchainBlock::create();
    $block->setPreviousHash(Util::hash($rand->string()));
    $block->setTimestamp(time());
    $block->setNonce(mt_rand(0, 10000));
    $block->setAuthor($this->configService->getBlockchainNodeId());
    $dataHandler = $this->getBlockDataHandler('raw::' . $rand->string());
    $block->setData($dataHandler->getRawData());

    return $block;
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockDataHandler($data = NULL) {

    $pluginId = $this->configService->getConfig()->get('dataHandler');
    if ($data) {
      if ($extractedId = $this->blockchainDataManager->extractPluginId($data)) {
        $pluginId = $extractedId;
      }
    }
    try {
      return $this->blockchainDataManager->createInstance($pluginId, [
        BlockchainDataInterface::DATA_KEY => $data,
      ]);
    } catch (\Exception $e) {
      $this->getLogger()
        ->error($e->getMessage() . $e->getTraceAsString());
      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(BlockchainBlockInterface $block) {

    $this->getBlockStorage()[] = $block;

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function loadByTimestampAndHash($timestamp, $previousHash) {

    foreach ($this->getBlockStorage() as $blockchainBlock) {
      if ($blockchainBlock->getTimestamp() == $timestamp &&
        $blockchainBlock->getPreviousHash() == $previousHash) {

        return $blockchainBlock;
      }
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function existsByTimestampAndHash($timestamp, $previousHash) {

    return (bool) $this->loadByTimestampAndHash($timestamp, $previousHash);
  }

  /**
   * {@inheritdoc}
   */
  public function getBlocksCountFrom(BlockchainBlockInterface $block) {

    $count = 0;
    foreach ($this->getBlockStorage() as $blockchainBlock) {
      if ($blockchainBlock->getTimestamp() > $block->getTimestamp()) {
        $count++;
      }
    }

    return $count;
  }

  /**
   * {@inheritdoc}
   */
  public function getBlocksFrom(BlockchainBlockInterface $block, $count) {

    $results = [];
    $fetched = 0;
    foreach ($this->getBlockStorage() as $blockchainBlock) {
      if ($blockchainBlock->getTimestamp() > $block->getTimestamp()) {
        $results[]= $block;
        $fetched++;
      }
      if ($fetched == $count) {
        break;
      }
    }

    return $results;
  }

  /**
   * {@inheritdoc}
   */
  public function createFromArray(array $values) {

    $block = BlockchainBlock::create([]);
    foreach ($values as $key => $value) {
      if (isset($block->$key)) {
        $block->{$key} = $value;
      }
    }

    return $block;
  }

}
