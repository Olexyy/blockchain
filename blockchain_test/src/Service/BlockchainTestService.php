<?php

namespace Drupal\blockchain_test\Service;


use Drupal\blockchain\Entity\BlockchainConfigInterface;
use Drupal\blockchain\Service\BlockchainServiceInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class BlockchainTestService.
 */
class BlockchainTestService implements BlockchainTestServiceInterface{

  /**
   * Blockchain service.
   *
   * @var BlockchainServiceInterface
   */
  protected $blockchainService;

  /**
   * Test context.
   *
   * @var TestCase
   */
  protected $testContext;

  /**
   * BlockchainTestService constructor.
   *
   * @param BlockchainServiceInterface $blockchainService
   *   Given service.
   */
  public function __construct(BlockchainServiceInterface $blockchainService) {

    $this->blockchainService = $blockchainService;
  }

  /**
   * Setter for test context.
   *
   * @param TestCase $testContext
   *   Test context.
   */
  public function setTestContext(TestCase $testContext) {

    $this->testContext = $testContext;
  }

  /**
   * Initializes configs.
   */
  public function initConfigs() {

    $this->blockchainService->getConfigService()->discoverBlockchainConfigs();
    $configs = $this->blockchainService->getConfigService()->getAll();
    $this->testContext->assertCount(2, $configs, '2 config created');
  }

  /**
   * Setter for current config.
   *
   * Can be set: 'blockchain_test_block' or 'blockchain_block'.
   *
   * @param string $configId
   *   Id of config.
   */
  public function setConfig($configId) {

    $isSet = $this->blockchainService->getConfigService()->setCurrentConfig($configId);
    $this->testContext->assertTrue($isSet, 'Blockchain config is set.');
    $currentConfig = $this->blockchainService->getConfigService()->getCurrentConfig();
    $this->testContext->assertInstanceOf(BlockchainConfigInterface::class, $currentConfig, 'Current config set.');
    $this->testContext->assertEquals($configId, $currentConfig->id(), 'Current config setting confirmed.');
  }

  /**
   * Sets specific count of blocks for storage.
   *
   * @param int $count
   *   Count of blocks to be set.
   *
   * @return int
   *   Count of affected blocks.
   */
  public function setBlockCount($count) {

    $affected = 0;
    if (is_numeric($count) && $count >= 0) {
      $blockCount = $this->blockchainService
        ->getStorageService()
        ->getBlockCount();
      $add = $remove = 0;
      if ($count > $blockCount) {
        $add = $count - $blockCount;
      }
      elseif ($count < $blockCount) {
        $remove = $blockCount - $count;
      }
      while ($add > 0 || $remove > 0) {
        if ($add) {
          if ($blockCount) {
            $this->blockchainService
              ->getStorageService()
              ->getRandomBlock(
                $this->blockchainService
                  ->getStorageService()
                  ->getLastBlock()
                  ->getHash()
              )->save();
          }
          else {
            $this->blockchainService
              ->getStorageService()
              ->getGenericBlock()
              ->save();
            $blockCount++;
          }
          $add --;
        }
        if ($remove) {
          $this->blockchainService
            ->getStorageService()
            ->getLastBlock()
            ->delete();
          $remove--;
        }
        $affected ++;
      }
      $blockCount = $this->blockchainService->getStorageService()->getBlockCount();
      $this->testContext->assertEquals($blockCount, $count, 'Target count equals');
      $validationResult = $this->blockchainService->getStorageService()->checkBlocks();
      $this->testContext->assertTrue($validationResult, 'Blocks in chain are valid');
    }

    return $affected;
  }

}
