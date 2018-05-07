<?php

namespace Drupal\blockchain\Service;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueWorkerInterface;
use Drupal\Core\Queue\QueueWorkerManagerInterface;
use Drupal\Core\Queue\SuspendQueueException;

/**
 * Class BlockchainQueueService.
 *
 * @package Drupal\blockchain\Service
 */
class BlockchainQueueService implements BlockchainQueueServiceInterface {

  /**
   * Queue factory service.
   *
   * @var QueueFactory
   */
  protected $queueFactory;

  /**
   * Queue worker manager.
   *
   * @var QueueWorkerManagerInterface
   */
  protected $queueWorkerManager;

  /**
   * Logger interface.
   *
   * @var LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * BlockchainQueueService constructor.
   *
   * @param QueueFactory $queueFactory
   *   Queue factory.
   * @param QueueWorkerManagerInterface $queueWorkerManager
   *   Queue worker manager.
   * @param LoggerChannelFactoryInterface $loggerChannelFactory
   *   Logger factory.
   */
  public function __construct(
    QueueFactory $queueFactory,
    QueueWorkerManagerInterface $queueWorkerManager,
    LoggerChannelFactoryInterface $loggerChannelFactory) {

    $this->queueFactory = $queueFactory;
    $this->queueWorkerManager = $queueWorkerManager;
    $this->loggerFactory = $loggerChannelFactory;
  }

  /**
   * Getter for logger.
   *
   * @return \Drupal\Core\Logger\LoggerChannelInterface
   *   Logger.
   */
  public function getLogger() {
    return $this->loggerFactory->get(static::LOGGER_CHANNEL);
  }

  /**
   * Gets blockchain queue.
   *
   * @return \Drupal\Core\Queue\QueueInterface
   *   Queue object.
   */
  public function getPool() {
    return $this->queueFactory->get(static::POOL_NAME);
  }

  /**
   * @return null|QueueWorkerInterface
   */
  public function getMiner() {
    try {
      return $this->queueWorkerManager->createInstance(static::POOL_NAME);
    } catch (\Exception $exception) {
      return NULL;
    }
  }

  /**
   * Queues block data to queue.
   *
   * @param mixed $blockData
   *   Given block data to be queued.
   */
  public function addBlock($blockData) {
    $item = (object) [
      'blockData' => $blockData,
    ];
    $this->getPool()->createItem($item);
  }

  /**
   * Processes mining.
   *
   * @param int $limit
   *   Limit of items to be processed.
   * @param int $leaseTime
   *   Time during which item will be processed.
   */
  public function doMining($limit = 0, $leaseTime = 3600) {

    $i = 0;
    while ($item = $this->getPool()->claimItem($leaseTime)) {
      if (!$limit || $i < $limit) {
        try {
          $this->getMiner()->processItem($item->data);
          $this->getPool()->deleteItem($item);
          $i++;
        } catch (SuspendQueueException $e) {
          $this->getPool()->releaseItem($item);
          break;
        } catch (\Exception $e) {
          $this->getLogger()
            ->error($e->getMessage() . $e->getTraceAsString());
        }
      }
    }
  }

}