<?php

namespace Drupal\blockchain_emulation\Service;

use Drupal\blockchain\Entity\BlockchainNodeInterface;
use Drupal\blockchain\Service\BlockchainNodeServiceInterface;

/**
 * Interface BlockchainEmulationNodeServiceInterface.
 *
 * @package Drupal\blockchain_emulation\Service
 */
interface BlockchainEmulationNodeServiceInterface extends BlockchainNodeServiceInterface {

  const LOGGER_CHANNEL_EMULATION = 'blockchain.emulation.node';

  const STORAGE_NAMESPACE = 'blockchain.emulation.node';

  /**
   * {@inheritdoc}
   *
   * @return BlockchainNodeInterface[]|array
   *   Returns array of Nodes if any.
   */
  public function getStorage();

  /**
   * Sets given count of nodes to emulation storage.
   *
   * @param int $count
   *   Target count.
   */
  public function setNodes($count);

  /**
   * Bulk remove from storage.
   *
   * @param int $count
   *   Count of nodes.
   */
  public function removeBlocks($count);

  /**
   * Bulk add to storage.
   *
   * @param int $count
   *   Count of nodes.
   */
  public function addBlocks($count);

  /**
   * Generates node.
   *
   * @return BlockchainNodeInterface
   *   Generated Node.
   */
  public function getRandomNode();

}
