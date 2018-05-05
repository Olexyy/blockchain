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

  /**
   * Check if blockchain is empty.
   *
   * @return bool
   *   Test result.
   */
  public function blockchainIsEmpty();

  /**
   * Getter for options of blockchain data plugins.
   *
   * @return array
   *   Options compatible array.
   */
  public function getBlockchainDataList();

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
   * Getter for blockchain data manager.
   *
   * @return BlockchainDataManager
   */
  public function getBlockchainDataManager();

}