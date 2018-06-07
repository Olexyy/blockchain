<?php

namespace Drupal\blockchain_emulation\Service;


use Drupal\blockchain\Entity\BlockchainBlockInterface;
use Drupal\blockchain\Plugin\BlockchainDataInterface;

/**
 * Interface BlockchainEmulationStorageServiceInterface.
 *
 * @package Drupal\blockchain\Service
 */
interface BlockchainEmulationStorageServiceInterface {

  const LOGGER_CHANNEL = 'blockchain.emulation.storage';

  const STORAGE_NAMESPACE = 'blockchain.emulation.storage';

  /**
   * Getter for logger.
   *
   * @return \Drupal\Core\Logger\LoggerChannelInterface
   */
  public function getLogger();

  /**
   * Getter for emulated storage.
   *
   * @return BlockchainBlockInterface[]|array
   *   Array of blocks if any.
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

  /**
   * Getter for generic block.
   *
   * @return BlockchainBlockInterface
   *   Given block.
   */
  function getGenericBlock();

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
   * Save handler.
   *
   * @param BlockchainBlockInterface $block
   *   Given block.
   *
   * @return mixed
   *   Execution result.
   */
  public function save(BlockchainBlockInterface $block);

  /**
   * Finds block in storage. If found, gets count created after blocks.
   *
   * @param BlockchainBlockInterface $block
   *   Given block.
   *
   * @return int
   *   Number of blocks.
   */
  public function getBlocksCountFrom(BlockchainBlockInterface $block);

  /**
   * Defines whether block exists.
   *
   * @param string $timestamp
   *   Timestamp param.
   * @param string $previousHash
   *   Previous hash param.
   *
   * @return bool
   *   Test result.
   */
  public function existsByTimestampAndHash($timestamp, $previousHash);

  /**
   * Defines whether block exists.
   *
   * @param string $timestamp
   *   Timestamp param.
   * @param string $previousHash
   *   Previous hash param.
   *
   * @return BlockchainBlockInterface|null
   *   Block if any.
   */
  public function loadByTimestampAndHash($timestamp, $previousHash);

  /**
   * Getter for blocks next to given one.
   *
   * @param BlockchainBlockInterface $block
   *   Given block.
   * @param string|int $count
   *   Numeric value - limit of blocks.
   * @return array
   *   Array of blocks as array.
   */
  public function getBlocksFrom(BlockchainBlockInterface $block, $count);

  /**
   * Factory method.
   *
   * @param array $values
   *   Array of values.
   *
   * @return BlockchainBlockInterface
   *   Blockchain block.
   */
  public function createFromArray(array $values);

}