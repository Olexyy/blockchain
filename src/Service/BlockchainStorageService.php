<?php

namespace Drupal\blockchain\Service;

use Drupal\blockchain\Entity\BlockchainBlock;
use Drupal\blockchain\Entity\BlockchainBlockInterface;
use Drupal\blockchain\Plugin\BlockchainDataInterface;
use Drupal\blockchain\Plugin\BlockchainDataManager;
use Drupal\blockchain\Utils\Util;
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
    } catch (\Exception $exception) {
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
      ->sort('id', 'DESC')
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

    $block = BlockchainBlock::create();
    $block->setPreviousHash(Util::hash('111'));
    $block->setTimestamp(time());
    $block->setNonce('111');
    $block->setAuthor($this->configService->getBlockchainNodeId());
    $dataHandler = $this->getBlockDataHandler(NULL);
    $dataHandler->setData('Generic block.');
    $block->setData($dataHandler->getData());

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
    } catch (\Exception $exception) {
      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(BlockchainBlockInterface $block) {

    try {
      return $this->getBlockStorage()->save($block);
    }
    catch (\Exception $exception) {
      return NULL;
    }
  }

}