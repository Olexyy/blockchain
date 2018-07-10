<?php

namespace Drupal\blockchain_test\Service;

use Drupal\blockchain\Entity\BlockchainConfigInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class BlockchainTestService.
 */
interface BlockchainTestServiceInterface {

  /**
   * Setter for test context.
   *
   * @param \PHPUnit\Framework\TestCase $testContext
   *   Test context.
   * @param null|string $baseUrl
   *   Base url.
   */
  public function setTestContext(TestCase $testContext, $baseUrl = NULL);

  /**
   * Initializes configs.
   *
   * @param bool $linked
   *   Defines if all configs will have same block node id.
   */
  public function initConfigs($linked = TRUE);

  /**
   * Setter for current config.
   *
   * Can be set: 'blockchain_test_block' or 'blockchain_block'.
   *
   * @param string $configId
   *   Id of config.
   */
  public function setConfig($configId);

  /**
   * Sets specific count of blocks for storage.
   *
   * @param int $count
   *   Count of blocks to be set.
   *
   * @return int
   *   Count of affected blocks.
   */
  public function setBlockCount($count);

  /**
   * Setter for blockchain type.
   *
   * This can be: BlockchainConfigInterface::TYPE_MULTIPLE|TYPE_SINGLE.
   *
   * @param string $type
   *   Type of blockchain.
   */
  public function setBlockchainType($type);

  /**
   * Creates simple blockchain node by given params.
   *
   * @param null|string $baseUrl
   *   Base url.
   * @param \Drupal\blockchain\Entity\BlockchainConfigInterface|null $blockchainConfig
   *   Blockchain config if any.
   */
  public function createNode($baseUrl = NULL, BlockchainConfigInterface $blockchainConfig = NULL);

}
