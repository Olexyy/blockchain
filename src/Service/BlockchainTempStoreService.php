<?php

namespace Drupal\blockchain\Service;

use Drupal\blockchain\Entity\BlockchainBlockInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\TempStore\SharedTempStoreFactory;

/**
 * Class BlockchainTempStoreService.
 *
 * @package Drupal\blockchain\Service
 */
class BlockchainTempStoreService implements BlockchainTempStoreServiceInterface {

  /**
   * Logger factory.
   *
   * @var LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * Shared temp store factory.
   *
   * @var SharedTempStoreFactory
   */
  protected $storeFactory;

  /**
   * Blockchain config service.
   *
   * @var BlockchainConfigServiceInterface
   */
  protected $blockchainConfigService;

  /**
   * Blockchain validator service.
   *
   * @var BlockchainValidatorServiceInterface
   */
  protected $blockchainValidatorService;

  /**
   * BlockchainStorageService constructor.
   *
   * @param SharedTempStoreFactory $storeFactory
   *   Store factory.
   * @param LoggerChannelFactoryInterface $loggerFactory
   *   Logger factory.
   * @param BlockchainConfigServiceInterface $blockchainConfigService
   *   Blockchain config.
   * @param BlockchainValidatorServiceInterface $blockchainValidatorService
   *   Validator service.
   */
  public function __construct(SharedTempStoreFactory $storeFactory,
                              LoggerChannelFactoryInterface $loggerFactory,
                              BlockchainConfigServiceInterface $blockchainConfigService,
                              BlockchainValidatorServiceInterface $blockchainValidatorService) {

    $this->loggerFactory = $loggerFactory;
    $this->storeFactory = $storeFactory;
    $this->blockchainConfigService = $blockchainConfigService;
    $this->blockchainValidatorService = $blockchainValidatorService;
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
  public function getBlockStorage() {

    $storageName = static::STORAGE_PREFIX . $this->blockchainConfigService->getCurrentConfig()->id();

    return $this->storeFactory->get($storageName);
  }

  /**
   * {@inheritdoc}
   */
  public function getAll() {

    $storage = $this->getBlockStorage();
    $data = $storage->get(static::BLOCKS_KEY);

    return  $data? $data : [];
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockCount() {

    return count($this->getAll());
  }

  /**
   * {@inheritdoc}
   */
  public function getLastBlock() {

    $data = $this->getAll();
    if ($count = count($data)) {

      return $data[$count-1];
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function anyBlock() {

    return (bool) $this->getBlockCount();
  }

  /**
   * {@inheritdoc}
   */
  public function loadByTimestampAndHash($timestamp, $previousHash) {

    $data = $this->getAll();
    foreach ($data as $blockchainBlock) {
      if ($blockchainBlock->getTimestamp() == $timestamp &&
        $blockchainBlock->getPreviousHash() == $previousHash) {

        return $blockchainBlock;
      }
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function existsByTimestampAndHash($timestamp, $previousHash) {

    return (bool) $this->loadByTimestampAndHash($timestamp, $previousHash);
  }

  /**
   * {@inheritdoc}
   */
  public function getBlocksCountFrom(BlockchainBlockInterface $block) {

    $data = $this->getAll();
    $count = 0;
    foreach ($data as $blockchainBlock) {
      if ($blockchainBlock->getTimestamp() > $block->getTimestamp()) {
        $count++;
      }
    }

    return $count;
  }

  /**
   * {@inheritdoc}
   */
  public function getBlocksFrom(BlockchainBlockInterface $block, $count, $asArray = TRUE) {

    $results = [];
    $fetched = 0;
    $data = $this->getAll();
    foreach ($data as $blockchainBlock) {
      if ($blockchainBlock->getTimestamp() > $block->getTimestamp()) {
        if ($asArray) {
          $results[]= $blockchainBlock->toArray();
        }
        else {
          $results[]= $blockchainBlock;
        }
        $fetched++;
      }
      if ($fetched == $count) {
        break;
      }
    }

    return $results;
  }

  /**
   * {@inheritdoc}
   */
  public function checkBlocks() {

    $previousBlock = NULL;
    foreach ($this->getBlockStorage() as $blockchainBlock) {
      if (!$this->blockchainValidatorService->blockIsValid($blockchainBlock, $previousBlock)) {

        return FALSE;
      }
      $previousBlock = $blockchainBlock;
    }

    return TRUE;
  }


  /**
   * {@inheritdoc}
   */
  public function save(BlockchainBlockInterface $blockchainBlock) {

    $data = $this->getAll();
    $data[]= $blockchainBlock;
    $this->getBlockStorage()->set(static::BLOCKS_KEY,  $data);
  }

  /**
   * {@inheritdoc}
   */
  public function delete($index = NULL) {

    $data = $this->getAll();
    if (is_numeric($index) && array_key_exists($index, $data)) {
      unset($data[$index]);
    }
    else {
      array_pop($data);
    }
    $data = array_values($data);
    $this->getBlockStorage()->set(static::BLOCKS_KEY,  $data);
  }

  /**
   * {@inheritdoc}
   */
  public function getFirstBlock() {

    if ($data = $this->getAll()) {
      return $data[0];
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getBlocks($offset = NULL, $limit = NULL ,$asArray = FALSE) {

    $results = [];
    $data = $this->getAll();
    foreach ($data as $index => $block) {
      if ($offset) {
        $offset--;
        continue;
      }
      $results[]= $block;
      if ($limit && count($results) == $limit) {
        break;
      }
    }
    if ($asArray) {
      foreach ($results as &$result) {
        $result = $result->toArray();
      }
    }

    return $results;
  }

  /**
   * Deletes all records.
   */
  public function deleteAll() {

    $storage = $this->getBlockStorage();
    $storage->delete(static::BLOCKS_KEY);
  }

}
