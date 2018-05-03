<?php

namespace Drupal\blockchain\Service;


use Drupal\blockchain\Entity\BlockchainBlockInterface;

/**
 * Interface BlockchainServiceInterface.
 *
 * @package Drupal\blockchain\Service
 */
interface BlockchainServiceInterface {

  /**
   * Getter for generic block.
   *
   * @return BlockchainBlockInterface
   *   Given block.
   */
  function getGenericBlock();

  /**
   * Getter for settings service.
   *
   * @return BlockchainConfigServiceInterface
   *   Config service.
   */
  public function getConfigService();

  /**
   * Getter for blockchain block storage.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface|null
   *   Storage object.
   */
  public function getBlockchainBlockStorage();

  /**
   * Getter for blockchain blocks count.
   *
   * @return int
   *   Number of items.
   */
  public function getBlockchainBlockCount();

}