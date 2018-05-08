<?php

namespace Drupal\blockchain\Service;
use Drupal\Core\Queue\QueueWorkerInterface;

/**
 * Interface BlockchainQueueServiceInterface.
 *
 * @package Drupal\blockchain\Service
 */
interface BlockchainQueueServiceInterface {

  const POOL_NAME = 'blockchain_pool';
  const LOGGER_CHANNEL = 'blockchain';

  /**
   * Getter for logger.
   *
   * @return \Drupal\Core\Logger\LoggerChannelInterface
   *   Logger.
   */
  public function getLogger();

  /**
   * Gets blockchain queue.
   *
   * @return \Drupal\Core\Queue\QueueInterface
   *   Queue object.
   */
  public function getPool();

  /**
   * Getter for miner plugin (worker).
   *
   * @return null|QueueWorkerInterface
   */
  public function getMiner();

  /**
   * Queues block data to queue.
   *
   * @param mixed $blockData
   *   Given block data to be queued.
   */
  public function addBlock($blockData);

  /**
   * Processes mining.
   *
   * @param int $limit
   *   Limit of items to be processed.
   * @param int $leaseTime
   *   Time during which item will be processed.
   */
  public function doMining($limit = 0, $leaseTime = 3600);



}