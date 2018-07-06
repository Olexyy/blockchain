<?php
/**
 * Created by PhpStorm.
 * User: oos
 * Date: 06.07.18
 * Time: 1:26
 */

namespace Drupal\blockchain\Service;


use Drupal\blockchain\Entity\BlockchainBlockInterface;

interface BlockchainTempStoreServiceInterface {

  const LOGGER_CHANNEL = 'blockchain.tempstore';
  const STORAGE_PREFIX = 'blockchain_tempstore_';
  const BLOCKS_KEY = 'blocks';

  /**
   * Getter for logger.
   *
   * @return \Drupal\Core\Logger\LoggerChannelInterface
   */
  public function getLogger();

  /**
   * Getter for blockchain block storage.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface|\Drupal\Core\TempStore\SharedTempStore|null
   *   Storage object.
   */
  public function getBlockStorage();

  /**
   * Getter for all blocks collection.
   *
   * @return array|BlockchainBlockInterface[]
   *   Blocks if any.
   */
  public function getAll();

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
   * @param bool $asArray
   *   Flag defines output format.
   *
   * @return array|BlockchainBlockInterface[]
   *   Array of blocks as array.
   */
  public function getBlocksFrom(BlockchainBlockInterface $block, $count, $asArray = TRUE);

  /**
   * Checks existing blocks in blockchain.
   *
   * Note that first bloc is checked only by nonce.
   * So if firs is not generic, previous block should
   * be fetched and checked.
   *
   * @return bool
   *   Test result.
   */
  public function checkBlocks();

  /**
   * Getter for first block.
   *
   * @return BlockchainBlockInterface|\Drupal\Core\Entity\EntityInterface|null
   *   Block if any.
   */
  public function getFirstBlock();

  /**
   * Getter for blocks.
   *
   * @param null|int $offset
   *   Offset.
   * @param null|int $limit
   *   Limit.
   * @param bool $asArray
   *   Defines output format.
   *
   * @return BlockchainBlockInterface[]|array
   *   Blocks if any.
   */
  public function getBlocks($offset = NULL, $limit = NULL, $asArray = FALSE);

  /**
   * Deletes all records.
   */
  public function deleteAll();

  /**
   * Deletes last block.
   *
   * @return BlockchainBlockInterface|null
   *   Block if any.
   */
  public function pop();

  /**
   * Deletes first block.
   *
   * @return BlockchainBlockInterface|null
   *   Block if any.
   */
  public function shift();

}