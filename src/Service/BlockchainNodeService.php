<?php

namespace Drupal\blockchain\Service;

use Drupal\blockchain\Entity\BlockchainNode;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Class BlockchainNodeService.
 *
 * @package Drupal\blockchain\Service
 */
class BlockchainNodeService implements BlockchainNodeServiceInterface {

  /**
   * Entity type manager.
   *
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * BlockchainNodeService constructor.
   *
   * @param EntityTypeManagerInterface $entityTypeManager
   *   Entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {

    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public function getStorage() {
    try {
      return $this->entityTypeManager
        ->getStorage(BlockchainNode::entityTypeId());
    } catch (\Exception $exception) {
      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getList() {
    return $this->getStorage()->loadMultiple();
  }

  /**
   * {@inheritdoc}
   */
  public function exists($id) {

    return (bool) $this->load($id);
  }

  /**
   * {@inheritdoc}
   */
  public function load($id) {

    return $this->getStorage()->load($id);
  }

}
