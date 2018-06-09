<?php

namespace Drupal\blockchain\Service;


use Drupal\blockchain\Entity\BlockchainBlock;
use Drupal\blockchain\Entity\BlockchainBlockInterface;
use Drupal\blockchain\Plugin\BlockchainDataInterface;
use Drupal\blockchain\Plugin\BlockchainDataManager;
use Drupal\blockchain\Utils\Util;
use Drupal\Component\Utility\Random;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

/**
 * Class BlockchainStorageService.
 *
 * @package Drupal\blockchain\Service
 */
class BlockchainStorageService implements BlockchainStorageServiceInterface {

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
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager,
                              LoggerChannelFactoryInterface $loggerFactory,
                              BlockchainConfigServiceInterface $blockchainSettingsService,
                              BlockchainDataManager $blockchainDataManager) {

    $this->entityTypeManager = $entityTypeManager;
    $this->loggerFactory = $loggerFactory;
    $this->configService = $blockchainSettingsService;
    $this->blockchainDataManager = $blockchainDataManager;
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

    try {
      return $this->entityTypeManager->getStorage('blockchain_block');
    } catch (\Exception $e) {
      $this->getLogger()
        ->error($e->getMessage() . $e->getTraceAsString());
      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockCount() {

    return $this->getBlockStorage()
      ->getQuery()
      ->accessCheck(FALSE)
      ->count()
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getLastBlock() {

    $blockId = $this->getBlockStorage()
      ->getQuery()
      ->accessCheck(FALSE)
      ->sort('timestamp', 'DESC')
      ->range(0,1)
      ->execute();
    if ($blockId) {
      return BlockchainBlock::load(current($blockId));
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
    $block = $this->getRandomBlock(Util::hash($rand->string()));
    $block->setNonce(mt_rand(0, 10000));

    return $block;
  }

  /**
   * {@inheritdoc}
   */
  public function getRandomBlock($previousHash) {

    $rand = new Random();
    $block = BlockchainBlock::create();
    $block->setPreviousHash(Util::hash($rand->string()));
    $block->setTimestamp(time());
    $block->setAuthor($this->configService->getBlockchainNodeId());
    $block->setData('raw::' . $rand->string(mt_rand(7, 20)));

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

    try {
      return $this->getBlockStorage()->save($block);
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

    $blockId = $this->getBlockStorage()
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('timestamp', $timestamp)
      ->condition('previous_hash', $previousHash)
      ->execute();
    if ($blockId) {
      return $this->getBlockStorage()->load(current($blockId));
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

    return $this->getBlockStorage()
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('timestamp', $block->getTimestamp(), '>')
      ->count()
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getBlocksFrom(BlockchainBlockInterface $block, $count) {

    $results = [];
    $blockIds = $this->getBlockStorage()
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('timestamp', $block->getTimestamp(), '>')
      ->sort('timestamp')
      ->range(0, $count)
      ->execute();
    foreach ($blockIds as $blockId) {
      $results[]= $this->getBlockStorage()->load($blockId)->toArray();
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