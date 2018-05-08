<?php

namespace Drupal\blockchain\Service;

use Drupal\blockchain\Entity\BlockchainBlockInterface;

/**
 * Interface BlockchainStorageServiceInterface.
 *
 * @package Drupal\blockchain\Service
 */
interface BlockchainStorageServiceInterface {

  /**
   * Getter for blockchain block storage.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface|null
   *   Storage object.
   */
  public function getBlockStorage();

  /**
   * Getter for blockchain blocks count.
   *
   * @return int
   *   Number of items.
   */
  public function getBlockCount();

  /**
   * Check if blockchain is empty.
   *
   * @return bool
   *   Test result.
   */
  public function anyBlock();

  /**
   * Getter for last block if any.
   *
   * @return BlockchainBlockInterface|null
   *   Blockchain block if any.
   */
  public function getLastBlock();

}