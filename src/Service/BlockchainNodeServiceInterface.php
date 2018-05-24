<?php

namespace Drupal\blockchain\Service;

use Drupal\blockchain\Entity\BlockchainNodeInterface;
use Drupal\blockchain\Utils\BlockchainRequestInterface;

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
   * @param int $offset
   *   Offset.
   * @param int $limit
   *   Limit.
   *
   * @return BlockchainNodeInterface[]
   *   Array of entities.
   */
  public function getList($offset = 0, $limit = 10);

  /**
   * Count query.
   *
   * @return int
   *   Count of items.
   */
  public function getCount();

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

  /**
   * Factory method.
   *
   * @param string $id
   *   Should be unique.
   * @param string $label
   *   Can be same as label.
   * @param string $ip
   *   Client ip.
   * @param string $port
   *   Client port.
   * @param bool $save
   *   Flag defines saving action.
   *
   * @return BlockchainNodeInterface|null
   *   New entity if created.
   */
  public function create($id, $label, $ip, $port, $save = TRUE);

  /**
   * Factory method.
   *
   * @param BlockchainRequestInterface $request
   *   Request.
   * @param bool $save
   *   Flag defines saving action.
   *
   * @return BlockchainNodeInterface|null
   *   New entity if created.
   */
  public function createFromRequest(BlockchainRequestInterface $request, $save = TRUE);

  /**
   * Delete handler.
   *
   * @param BlockchainNodeInterface $blockchainNode
   *   Given entity.
   *
   * @return bool
   *   Execution result.
   */
  public function delete(BlockchainNodeInterface $blockchainNode);

}