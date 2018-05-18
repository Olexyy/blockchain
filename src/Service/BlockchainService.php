<?php

namespace Drupal\blockchain\Service;

use Drupal\blockchain\Entity\BlockchainBlock;
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
   * Blockchain API service.
   *
   * @var BlockchainApiServiceInterface
   */
  protected $blockchainApiService;

  /**
   * Blockchain Node service.
   *
   * @var BlockchainNodeServiceInterface
   */
  protected $blockchainNodeService;

  /**
   * Blockchain validator service.
   *
   * @var BlockchainValidatorServiceInterface
   */
  protected $blockchainValidatorService;

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
   * @param BlockchainApiServiceInterface $blockchainApiService
   *   Given Blockchain API service.
   * @param BlockchainNodeServiceInterface $blockchainNodeService
   *   Given Blockchain Node service.
   * @param BlockchainValidatorServiceInterface $blockchainValidatorService
   *   Given Blockchain Validate service.
   */
  public function __construct(
    BlockchainConfigServiceInterface $blockchainSettingsService,
    BlockchainStorageServiceInterface $blockchainStorageService,
    BlockchainDataManager $blockchainDataManager,
    BlockchainQueueServiceInterface $blockchainQueueService,
    BlockchainApiServiceInterface $blockchainApiService,
    BlockchainNodeServiceInterface $blockchainNodeService,
    BlockchainValidatorServiceInterface $blockchainValidatorService) {

    $this->blockchainServiceSettings = $blockchainSettingsService;
    $this->blockchainDataManager = $blockchainDataManager;
    $this->blockchainStorageService = $blockchainStorageService;
    $this->blockchainQueueService = $blockchainQueueService;
    $this->blockchainApiService = $blockchainApiService;
    $this->blockchainNodeService = $blockchainNodeService;
    $this->blockchainValidatorService = $blockchainValidatorService;
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
  public function getApiService() {
    return $this->blockchainApiService;
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
  public function getBlockDataHandler($data = NULL) {

    $pluginId = $this->getConfigService()->getConfig()->get('dataHandler');
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
  public static function instance() {

    return \Drupal::service('blockchain.service');
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockchainNodeService() {

    return $this->blockchainNodeService;
  }

  /**
   * {@inheritdoc}
   */
  public function getValidatorService() {

    return $this->blockchainValidatorService;
  }

}
