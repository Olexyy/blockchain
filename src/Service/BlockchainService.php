<?php

namespace Drupal\blockchain\Service;

use Drupal\blockchain\Plugin\BlockchainAuthManager;
use Drupal\blockchain\Plugin\BlockchainDataManager;

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
   * Blockchain miner service.
   *
   * @var BlockchainMinerServiceInterface
   */
  protected $blockchainMinerService;

  /**
   * Blockchain locker service.
   *
   * @var BlockchainLockerServiceInterface
   */
  protected $blockchainLockerService;

  /**
   * Auth manager.
   *
   * @var BlockchainAuthManager
   */
  protected $blockchainAuthManager;

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
   * @param BlockchainMinerServiceInterface $blockchainMinerService
   *   Given Blockchain miner service.
   * @param BlockchainLockerServiceInterface $blockchainLockerService
   *   Blockchain locker service.
   * @param BlockchainAuthManager $blockchainAuthManager
   *   Blockchain auth manager.
   */
  public function __construct(
    BlockchainConfigServiceInterface $blockchainSettingsService,
    BlockchainStorageServiceInterface $blockchainStorageService,
    BlockchainDataManager $blockchainDataManager,
    BlockchainQueueServiceInterface $blockchainQueueService,
    BlockchainApiServiceInterface $blockchainApiService,
    BlockchainNodeServiceInterface $blockchainNodeService,
    BlockchainValidatorServiceInterface $blockchainValidatorService,
    BlockchainMinerServiceInterface $blockchainMinerService,
    BlockchainLockerServiceInterface $blockchainLockerService,
    BlockchainAuthManager $blockchainAuthManager) {

    $this->blockchainServiceSettings = $blockchainSettingsService;
    $this->blockchainDataManager = $blockchainDataManager;
    $this->blockchainStorageService = $blockchainStorageService;
    $this->blockchainQueueService = $blockchainQueueService;
    $this->blockchainApiService = $blockchainApiService;
    $this->blockchainNodeService = $blockchainNodeService;
    $this->blockchainValidatorService = $blockchainValidatorService;
    $this->blockchainMinerService = $blockchainMinerService;
    $this->blockchainLockerService = $blockchainLockerService;
    $this->blockchainAuthManager = $blockchainAuthManager;
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
  public function getDataManager() {
    return $this->blockchainDataManager;
  }

  /**
   * {@inheritdoc}
   */
  public function getNodeService() {

    return $this->blockchainNodeService;
  }

  /**
   * {@inheritdoc}
   */
  public function getValidatorService() {

    return $this->blockchainValidatorService;
  }

  /**
   * {@inheritdoc}
   */
  public function getMinerService() {

    return $this->blockchainMinerService;
  }

  /**
   * {@inheritdoc}
   */
  public function getLockerService() {

    return $this->blockchainLockerService;
  }

  /**
   * {@inheritdoc}
   */
  public function getAuthManager() {

    return $this->blockchainAuthManager;
  }

}
