<?php

namespace Drupal\blockchain\Service;
use Drupal\Core\Queue\QueueWorkerInterface;

/**
 * Interface BlockchainQueueServiceInterface.
 *
 * @package Drupal\blockchain\Service
 */
interface BlockchainQueueServiceInterface {

  const BLOCK_POOL_NAME = 'blockchain_pool';
  const ANNOUNCE_QUEUE_NAME = 'announce_queue';
  const LOGGER_CHANNEL = 'blockchain.queue';
  const ANNOUNCE_QUEUE_ITEM = 'announce_item';
  const BLOCKCHAIN_TYPE_ID = 'blockchain_type_id';

  /**
   * Getter for logger.
   *
   * @return \Drupal\Core\Logger\LoggerChannelInterface
   *   Logger.
   */
  public function getLogger();

  /**
   * Gets blockchain block queue.
   *
   * @return \Drupal\Core\Queue\QueueInterface
   *   Queue object.
   */
  public function getBlockPool();

  /**
   * Gets blockchain announce queue.
   *
   * @return \Drupal\Core\Queue\QueueInterface
   *   Queue object.
   */
  public function getAnnounceQueue();

  /**
   * Getter for miner plugin (worker).
   *
   * @return null|QueueWorkerInterface
   */
  public function getMiner();

  /**
   * Getter for announce handler plugin (worker).
   *
   * @return null|QueueWorkerInterface
   */
  public function getAnnounceHandler();

  /**
   * Queues block data to queue.
   *
   * @param mixed $blockData
   *   Given block data to be queued.
   * @param string $blockchainTypeId
   *   Type of blockchain.
   */
  public function addBlockItem($blockData, $blockchainTypeId);

  /**
   * Queues announce data to queue.
   *
   * @param mixed $announceData
   *   Given announce data to be queued.
   * @param string $blockchainTypeId
   *   Type of blockchain.
   */
  public function addAnnounceItem($announceData, $blockchainTypeId);

  /**
   * Processes mining.
   *
   * @param int $limit
   *   Limit of items to be processed.
   * @param int $leaseTime
   *   Time during which item will be processed.
   *
   * @return int
   *   Count of processed items.
   */
  public function doMining($limit = 0, $leaseTime = 3600);

  /**
   * Processes announce handling.
   *
   * @param int $limit
   *   Limit of items to be processed.
   * @param int $leaseTime
   *   Time during which item will be processed.
   *
   * @return int
   *   Count of processed items.
   */
  public function doAnnounceHandling($limit = 0, $leaseTime = 3600);

}