<?php

namespace Drupal\blockchain\Service;
use Drupal\blockchain\Entity\BlockchainNodeInterface;

/**
 * Interface BlockchainNodeServiceInterface.
 *
 * @package Drupal\blockchain\Service
 */
interface BlockchainNodeServiceInterface {

  /**
   * Getter for storage.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface|null
   */
  public function getStorage();

  /**
   * Getter for list of Blockchain nodes.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   Array of entities.
   */
  public function getList();

  /**
   * Getter for list of Blockchain nodes.
   *
   * @param string $id
   *   Given id.
   *
   * @return bool
   *   Test result.
   */
  public function exists($id);

  /**
   * Getter for list of Blockchain nodes.
   *
   * @param string $id
   *   Given id.
   *
   * @return BlockchainNodeInterface|null
   *   Entity if any.
   */
  public function load($id);

}