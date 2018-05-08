<?php

namespace Drupal\blockchain\Service;

use Drupal\blockchain\Entity\BlockchainBlock;
use Drupal\blockchain\Entity\BlockchainBlockInterface;
use Drupal\blockchain\Plugin\BlockchainDataInterface;
use Drupal\blockchain\Plugin\BlockchainDataManager;
use Drupal\blockchain\Utils\Util;

/**
 * Class BlockchainService.
 *
 * @package Drupal\blockchain\Service
 */
class BlockchainService implements BlockchainServiceInterface {

  /**
   * BlockchainSettingsService.
   *
   * @var BlockchainConfigServiceInterface
   */
  protected $blockchainServiceSettings;

  /**
   * Blockchain data manager.
   *
   * @var BlockchainDataManager
   */
  protected $blockchainDataManager;

  /**
   * Blockchain storage service.
   *
   * @var BlockchainStorageServiceInterface
   */
  protected $blockchainStorageService;

  /**
   * Blockchain queue service.
   *
   * @var BlockchainQueueServiceInterface
   */
  protected $blockchainQueueService;

  /**
   * BlockchainService constructor.
   *
   * @param BlockchainConfigServiceInterface $blockchainSettingsService
   *   Given service.
   * @param BlockchainStorageServiceInterface $blockchainStorageService
   *   Given service.
   * @param BlockchainDataManager $blockchainDataManager
   *   Given blockchain data manager.
   * @param BlockchainQueueServiceInterface $blockchainQueueService
   *   Given queue service.
   */
  public function __construct(
    BlockchainConfigServiceInterface $blockchainSettingsService,
    BlockchainStorageServiceInterface $blockchainStorageService,
    BlockchainDataManager $blockchainDataManager,
    BlockchainQueueServiceInterface $blockchainQueueService) {

    $this->blockchainServiceSettings = $blockchainSettingsService;
    $this->blockchainDataManager = $blockchainDataManager;
    $this->blockchainStorageService = $blockchainStorageService;
    $this->blockchainQueueService = $blockchainQueueService;
  }

  /**
   * {@inheritdoc}
   */
  public function getStorageService() {
    return $this->blockchainStorageService;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfigService() {

    return $this->blockchainServiceSettings;
  }

  /**
   * {@inheritdoc}
   */
  public function getQueueService() {

    return $this->blockchainQueueService;
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockchainDataManager() {
    return $this->blockchainDataManager;
  }

  /**
   * {@inheritdoc}
   */
  public function getGenericBlock() {

    $block = BlockchainBlock::create();
    $block->setPreviousHash(Util::hash('111'));
    $block->setTimestamp(time());
    $block->setNonce('111');
    $block->setAuthor($this->getConfigService()->getBlockchainNodeId());
    $this->getBlockDataHandler($block)->setData('Generic block.');

    return $block;
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockDataHandler(BlockchainBlockInterface $block) {

    $pluginId = $this->getConfigService()->getConfig()->get('dataHandler');
    if ($data = $block->getData()) {
      if ($extractedId = $this->blockchainDataManager->extractPluginId($data)) {
        $pluginId = $extractedId;
      }
    }
    try {
      return $this->blockchainDataManager->createInstance($pluginId, [
        BlockchainDataInterface::BLOCK_KEY => $block,
      ]);
    } catch (\Exception $exception) {
      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function hashIsValid($hash) {

    $powPosition = $this->getConfigService()->getPowPosition();
    $powExpression = $this->getConfigService()->getPowExpression();
    $length = strlen($powExpression);
    if ($powPosition === BlockchainConfigServiceInterface::POW_POSITION_START) {
      if (substr($hash, 0, $length) === $powExpression) {
        return TRUE;
      }
    }
    else {
      if (substr($hash, -$length) === $powExpression) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public static function instance() {
    return \Drupal::service('blockchain.service');
  }

}