<?php

namespace Drupal\blockchain_emulation\Service;


use Drupal\blockchain\Entity\BlockchainBlock;
use Drupal\blockchain\Entity\BlockchainBlockInterface;
use Drupal\blockchain\Plugin\BlockchainDataInterface;
use Drupal\blockchain\Plugin\BlockchainDataManager;
use Drupal\blockchain\Service\BlockchainConfigServiceInterface;
use Drupal\blockchain\Service\BlockchainMinerServiceInterface;
use Drupal\blockchain\Service\BlockchainStorageServiceInterface;
use Drupal\blockchain\Service\BlockchainValidatorServiceInterface;
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
   * Blockchain miner service.
   *
   * @var BlockchainMinerServiceInterface
   */
  protected $minerService;

  /**
   * Blockchain storage service.
   *
   * @var BlockchainStorageServiceInterface
   */
  protected $blockchainStorageService;

  /**
   * Blockchain validator.
   *
   * @var BlockchainValidatorServiceInterface
   */
  public $blockchainValidatorService;

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
   * @param BlockchainMinerServiceInterface $minerService
   *   Blockchain miner service.
   * @param BlockchainStorageServiceInterface $blockchainStorageService
   *   Blockchain storage service.
   * @param BlockchainValidatorServiceInterface $blockchainValidatorService
   *   Blockchain validator.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager,
                              LoggerChannelFactoryInterface $loggerFactory,
                              BlockchainConfigServiceInterface $blockchainSettingsService,
                              BlockchainDataManager $blockchainDataManager,
                              StateInterface $state,
                              BlockchainMinerServiceInterface $minerService,
                              BlockchainStorageServiceInterface $blockchainStorageService,
                              BlockchainValidatorServiceInterface $blockchainValidatorService) {

    $this->entityTypeManager = $entityTypeManager;
    $this->loggerFactory = $loggerFactory;
    $this->configService = $blockchainSettingsService;
    $this->blockchainDataManager = $blockchainDataManager;
    $this->state = $state;
    $this->minerService = $minerService;
    $this->blockchainStorageService = $blockchainStorageService;
    $this->blockchainValidatorService = $blockchainValidatorService;
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
  public function addToStorage(BlockchainBlockInterface $blockchainBlock) {

    $data = $this->getBlockStorage();
    $data[]= $blockchainBlock;
    $this->state->set(static::STORAGE_NAMESPACE,  $data);
  }

  /**
   * {@inheritdoc}
   */
  public function removeFromStorage($index = NULL) {

    $data = $this->getBlockStorage();
    if (is_numeric($index) && array_key_exists($index, $data)) {
      unset($data[$index]);
    }
    else {
      array_pop($data);
    }
    $data = array_values($data);
    $this->state->set(static::STORAGE_NAMESPACE,  $data);
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

  /**
   * {@inheritdoc}
   */
  public function addBlocks($count) {

    for ($i = 0; $i < $count; $i++) {
      if ($this->getBlockCount()) {
        $block = $this->blockchainStorageService->getRandomBlock(
          $this->getLastBlock()->getHash()
        );
      }
      else {
        $block = $this->blockchainStorageService->getGenericBlock();
      }
      $this->addToStorage($block);
      // Emulate timestamp.
      sleep(1);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function removeBlocks($count) {

    for ($i = 0; $i < $count; $i++) {
      $this->removeFromStorage();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function setBlocks($count) {

    $existingCount = $this->getBlockCount();
    if ($existingCount > $count) {
      $this->removeBlocks($existingCount - $count);
    }
    else if ($existingCount < $count) {
      $this->addBlocks($count - $existingCount);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function checkBlocks() {

    $previousBlock = NULL;
    foreach ($this->getBlockStorage() as $blockchainBlock) {
      if (!$this->blockchainValidatorService->blockIsValid($blockchainBlock, $previousBlock)) {

        return FALSE;
      }
      $previousBlock = $blockchainBlock;
    }

    return TRUE;
  }

}
