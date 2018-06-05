<?php

namespace Drupal\blockchain\Service;

use Drupal\blockchain\Plugin\BlockchainDataInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Queue\QueueFactory;
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
   * {@inheritdoc}
   */
  public function getLogger() {

    return $this->loggerFactory->get(static::LOGGER_CHANNEL);
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPool() {

    return $this->queueFactory->get(static::BLOCK_POOL_NAME);
  }

  /**
   * {@inheritdoc}
   */
  public function getMiner() {

    try {
      return $this->queueWorkerManager->createInstance(static::BLOCK_POOL_NAME);
    } catch (\Exception $exception) {
      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function addBlockItem($blockData) {

    $item = (object) [
      BlockchainDataInterface::DATA_KEY => $blockData,
    ];
    $this->getBlockPool()->createItem($item);
  }

  /**
   * {@inheritdoc}
   */
  public function doMining($limit = 0, $leaseTime = 3600) {

    $i = 0;
    while ($item = $this->getBlockPool()->claimItem($leaseTime)) {
      if (!$limit || $i < $limit) {
        try {
          $this->getMiner()->processItem($item->data);
          $this->getBlockPool()->deleteItem($item);
          $i++;
        } catch (SuspendQueueException $e) {
          $this->getBlockPool()->releaseItem($item);
          break;
        } catch (\Exception $e) {
          $this->getLogger()
            ->error($e->getMessage() . $e->getTraceAsString());
        }
      }
    }

    return $i;
  }

  /**
   * {@inheritdoc}
   */
  public function getAnnounceQueue() {

    return $this->queueFactory->get(static::ANNOUNCE_QUEUE_NAME);
  }

  /**
   * {@inheritdoc}
   */
  public function getAnnounceHandler() {
    try {
      return $this->queueWorkerManager->createInstance(static::ANNOUNCE_QUEUE_NAME);
    } catch (\Exception $exception) {
      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function addAnnounceItem($announceData) {

    $item = (object) [
      static::ANNOUNCE_QUEUE_ITEM => $announceData,
    ];
    $this->getAnnounceQueue()->createItem($item);
  }

  /**
   * {@inheritdoc}
   */
  public function doAnnounceHandling($limit = 0, $leaseTime = 3600) {

    $i = 0;
    while ($item = $this->getAnnounceQueue()->claimItem($leaseTime)) {
      if (!$limit || $i < $limit) {
        try {
          $this->getAnnounceHandler()->processItem($item->data);
          $this->getAnnounceQueue()->deleteItem($item);
          $i++;
        } catch (SuspendQueueException $e) {
          $this->getAnnounceQueue()->releaseItem($item);
          break;
        } catch (\Exception $e) {
          $this->getAnnounceQueue()->deleteItem($item);
          $this->getLogger()
            ->error($e->getMessage() . $e->getTraceAsString());
        }
      }
    }

    return $i;
  }
}