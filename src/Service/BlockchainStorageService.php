<?php

namespace Drupal\blockchain\Service;
use Drupal\blockchain\Entity\BlockchainBlock;
use Drupal\Core\Entity\EntityTypeManagerInterface;

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
   * BlockchainStorageService constructor.
   *
   * @param EntityTypeManagerInterface $entityTypeManager
   *   Given service.
   */
  public function __construct(
    EntityTypeManagerInterface $entityTypeManager) {

    $this->entityTypeManager = $entityTypeManager;
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