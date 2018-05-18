<?php

namespace Drupal\blockchain\Service;

use Drupal\blockchain\Entity\BlockchainBlockInterface;
use Drupal\blockchain\Plugin\BlockchainDataInterface;
use Drupal\blockchain\Plugin\BlockchainDataManager;

/**
 * Interface BlockchainServiceInterface.
 *
 * @package Drupal\blockchain\Service
 */
interface BlockchainServiceInterface {

  /**
   * Getter for settings service.
   *
   * @return BlockchainConfigServiceInterface
   *   Config service.
   */
  public function getConfigService();

  /**
   * Getter for blockchain storage service.
   *
   * @return BlockchainStorageServiceInterface
   *   Service object.
   */
  public function getStorageService();

  /**
   * Blockchain queue service.
   *
   * @return BlockchainQueueServiceInterface
   *   Service object.
   */
  public function getQueueService();

  /**
   * Getter for API service.
   *
   * @return BlockchainApiServiceInterface
   *   Service object.
   */
  public function getApiService();

  /**
   * Getter for generic block.
   *
   * @return BlockchainBlockInterface
   *   Given block.
   */
  function getGenericBlock();

  /**
   * Blockchain data manager.
   *
   * @return BlockchainDataManager
   *   Service.
   */
  public function getBlockchainDataManager();

  /**
   * Getter for blockchain data handler.
   *
   * @param string|null $data
   *   Block to be handled.
   *
   * @return BlockchainDataInterface
   *   Given handler.
   */
  public function getBlockDataHandler($data = NULL);

  /**
   * Static call for service.
   *
   * @return BlockchainServiceInterface
   *   Service instance.
   */
  public static function instance();

  /**
   * Getter for Blockchain Node service.
   *
   * @return BlockchainNodeServiceInterface
   *   Service object.
   */
  public function getBlockchainNodeService();

  /**
   * Getter for validator service.
   *
   * @return BlockchainValidatorServiceInterface
   *   Service object.
   */
  public function getValidatorService();

}
