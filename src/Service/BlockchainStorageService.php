<?php

namespace Drupal\blockchain\Service;

use Drupal\blockchain\Entity\BlockchainBlock;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

/**
 * Class BlockchainStorageService.
 *
 * @package Drupal\blockchain\Service
 */
class BlockchainStorageService implements BlockchainStorageServiceInterface {

  /**
   * Entity type manager.
   *
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Logger factory.
   *
   * @var LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * Blockchain config service.
   *
   * @var BlockchainConfigServiceInterface
   */
  protected $configService;


  /**
   * BlockchainStorageService constructor.
   *
   * @param EntityTypeManagerInterface $entityTypeManager
   *   Given service.
   * @param LoggerChannelFactoryInterface $loggerFactory
   *   Logger factory.
   * @param BlockchainConfigServiceInterface $blockchainSettingsService
   *   Blockchain config service.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager,
                              LoggerChannelFactoryInterface $loggerFactory,
                              BlockchainConfigServiceInterface $blockchainSettingsService) {

    $this->entityTypeManager = $entityTypeManager;
    $this->loggerFactory = $loggerFactory;
    $this->configService = $blockchainSettingsService;
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

    try {
      return $this->entityTypeManager->getStorage('blockchain_block');
    } catch (\Exception $exception) {
      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockCount() {

    return $this->getBlockStorage()
      ->getQuery()
      ->accessCheck(FALSE)
      ->count()
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getLastBlock() {

    $blockId = $this->getBlockStorage()
      ->getQuery()
      ->accessCheck(FALSE)
      ->sort('id', 'DESC')
      ->range(0,1)
      ->execute();
    if ($blockId) {
      return BlockchainBlock::load(current($blockId));
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function anyBlock() {

    return (bool) $this->getBlockCount();
  }

}