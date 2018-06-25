<?php

namespace Drupal\blockchain_test\Service;

use PHPUnit\Framework\TestCase;

/**
 * Class BlockchainTestService.
 */
interface BlockchainTestServiceInterface {



  /**
   * Setter for test context.
   *
   * @param TestCase $testContext
   *   Test context.
   */
  public function setTestContext(TestCase $testContext);

  /**
   * Initializes configs.
   */
  public function initConfigs();

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

}
