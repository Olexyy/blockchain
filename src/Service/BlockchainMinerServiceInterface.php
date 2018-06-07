<?php

namespace Drupal\blockchain\Service;

use Drupal\blockchain\Entity\BlockchainBlockInterface;

/**
 * Interface BlockchainMinerServiceInterface.
 *
 * @package Drupal\blockchain\Service
 */
interface BlockchainMinerServiceInterface {

  /**
   * Mining procedure.
   *
   * @param string $miningString
   *   Given value.
   *
   * @return int
   *   Nonce.
   */
  public function mine($miningString);

  /**
   * Block, with all values set except for nonce.
   *
   * @param BlockchainBlockInterface $blockchainBlock
   *   Given blockchain block.
   */
  public function mineBlock(BlockchainBlockInterface $blockchainBlock);
}
