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
  public function getList($offset = NULL, $limit = NULL);

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
   * @param string $self
   *   Given id.
   * @param string $blockchainTypeId
   *   Blockchain type id.
   *
   * @return bool
   *   Test result.
   */
  public function existsBySelfAndType($self, $blockchainTypeId);

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
   * Loads node by self and type.
   *
   * @param string $self
   *   Given id.
   * @param string $blockchainTypeId
   *   Blockchain type id.
   *
   * @return BlockchainNodeInterface|null
   *   Entity if any.
   */
  public function loadBySelfAndType($self, $blockchainTypeId);

  /**
   * Factory method.
   *
   * @param string $blockchainType
   *   Type of blockchain.
   * @param $self
   *   Actual id of blockchain.
   * @param $addressSource
   *   Address source.
   * @param string $address
   *   Client ip/host.
   * @param string $port
   *   Client port.
   * @param null $secure
   *   Secure flag.
   * @param string $label
   *   Can be same as label.
   * @param bool $save
   *   Flag defines saving action.
   *
   * @return BlockchainNodeInterface|null
   *   New entity if created.
   */
  public function create($blockchainType, $self, $addressSource,  $address, $port = NULL, $secure = NULL, $label = NULL, $save = TRUE);

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

  /**
   * Save handler.
   *
   * @param BlockchainNodeInterface $blockchainNode
   *   Given entity.
   *
   * @return bool
   *   Execution result.
   */
  public function save(BlockchainNodeInterface $blockchainNode);

}