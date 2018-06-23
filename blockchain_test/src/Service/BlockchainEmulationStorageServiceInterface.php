<?php

namespace Drupal\blockchain_emulation\Service;


use Drupal\blockchain\Entity\BlockchainBlockInterface;
use Drupal\blockchain\Service\BlockchainStorageServiceInterface;

/**
 * Interface BlockchainEmulationStorageServiceInterface.
 *
 * @package Drupal\blockchain\Service
 */
interface BlockchainEmulationStorageServiceInterface extends BlockchainStorageServiceInterface {

  const LOGGER_CHANNEL_EMULATION = 'blockchain.emulation.storage';

  const STORAGE_NAMESPACE = 'blockchain.emulation.storage';

  /**
   * Adds block to storage.
   *
   * @param BlockchainBlockInterface $blockchainBlock
   *   Block to add.
   */
  public function addToStorage(BlockchainBlockInterface $blockchainBlock);

  /**
   * Removes last block or defined in index.
   *
   * @param null|int $index
   *   If defined, deletes by index.
   */
  public function removeFromStorage($index = NULL);

  /**
   * Adds given count of blocks.
   *
   * @param int $count
   *   Count of blocks to add.
   */
  public function addBlocks($count);

  /**
   * Removes given count of blocks.
   *
   * @param int $count
   *   Count of blocks to remove.
   */
  public function removeBlocks($count);

  /**
   * Sets given count of blocks.
   *
   * @param int $count
   *   Sets given count of blocks.
   */
  public function setBlocks($count);

  /**
   * {@inheritdoc}
   *
   * @return BlockchainBlockInterface[]|array
   *   Blockchain blocks if any.
   */
  public function getBlockStorage();

}
