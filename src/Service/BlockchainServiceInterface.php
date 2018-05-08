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
   * @param BlockchainBlockInterface $block
   *   Block to be handled.
   *
   * @return BlockchainDataInterface
   *   Given handler.
   */
  public function getBlockchainDataHandler(BlockchainBlockInterface $block);

  /**
   * Validates hash according to given Pow rules.
   *
   * @param string $hash
   *   Hash.
   *
   * @return bool
   *   Test result.
   */
  public function hashIsValid($hash);

}
