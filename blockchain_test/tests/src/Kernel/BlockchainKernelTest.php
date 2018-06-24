<?php

namespace Drupal\Tests\blockchain_test\Kernel;

use Drupal\blockchain\Entity\BlockchainBlockInterface;
use Drupal\blockchain\Entity\BlockchainConfigInterface;
use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\KernelTests\KernelTestBase;

/**
 * Tests blockchain.
 *
 * @group blockchain
 */
class BlockchainKernelTest extends KernelTestBase {

  /**
   * Blockchain service.
   *
   * @var BlockchainServiceInterface
   */
  protected $blockchainService;

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = ['blockchain', 'blockchain_test'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {

    parent::setUp();
    parent::setUp();
    $this->installConfig('blockchain');
    $this->installConfig('blockchain_test');
    $this->installEntitySchema('blockchain_block');
    $this->installEntitySchema('blockchain_test_block');
    $this->installEntitySchema('blockchain_node');
    $this->installEntitySchema('blockchain_config');
    $this->blockchainService = $this->container->get('blockchain.service');
    $this->assertInstanceOf(BlockchainServiceInterface::class, $this->blockchainService,
      'Blockchain service instantiated.');
    $this->blockchainService->getConfigService()->discoverBlockchainConfigs();
    $configs = $this->blockchainService->getConfigService()->getAll();
    $this->assertCount(2, $configs, '2 config created');
    $this->blockchainService->getConfigService()->setCurrentConfig('blockchain_test_block');
    $currentConfig = $this->blockchainService->getConfigService()->getCurrentConfig();
    $this->assertInstanceOf(BlockchainConfigInterface::class, $currentConfig, 'Current config set.');
    $this->assertEquals('blockchain_test_block', $currentConfig->id(), 'Current config set to test.');
  }

  /**
   * Test emulation storage.
   */
  public function testEmulationStorage() {

    $count = $this->blockchainService->getStorageService()->getBlockCount();
    $this->assertEmpty($count, 'No blocks in storage');
    $this->assertFalse($this->blockchainService->getStorageService()->anyBlock(), 'Any block not found');
    $this->blockchainService->getStorageService()->getGenericBlock()->save();
    $count = $this->blockchainService->getStorageService()->getBlockCount();
    $this->assertEquals(1, $count, 'Set count of blocks to 1');
    $this->assertTrue($this->blockchainService->getStorageService()->anyBlock(), 'Any block found');
    $lastBlock = $this->blockchainService->getStorageService()->getLastBlock();
    $this->assertInstanceOf(BlockchainBlockInterface::class, $lastBlock, 'Last block obtained.');
    for ($i = 0; $i < 4; $i++) {
      $this->blockchainService
        ->getStorageService()
        ->getRandomBlock(
          $this->blockchainService
            ->getStorageService()
            ->getLastBlock()
            ->getHash()
        )->save();
    }
    $count = $this->blockchainService->getStorageService()->getBlockCount();
    $this->assertTrue($this->blockchainService->getStorageService()->anyBlock(), 'Any block found');
    $this->assertEquals(5, $count, 'Set count of blocks to 5');
    for ($i = 0; $i < 2; $i++) {
      $this->blockchainService
        ->getStorageService()
        ->getLastBlock()->delete();
    }
    $count = $this->blockchainService->getStorageService()->getBlockCount();
    $this->assertEquals(3, $count, 'Set count of blocks to 3');
    for ($i = 0; $i < 4; $i++) {
      $this->blockchainService
        ->getStorageService()
        ->getRandomBlock(
          $this->blockchainService
            ->getStorageService()
            ->getLastBlock()
            ->getHash()
        )->save();
    }
    $checkLast = $this->blockchainService->getValidatorService()->blockIsValid(
      $this->blockchainService
      ->getStorageService()
      ->getLastBlock()
    );
    $this->assertEquals(3, $count, 'Set count of blocks to 7');
    $this->assertTrue($checkLast, 'Last block is valid');
    $validationResult = $this->blockchainService->getStorageService()->checkBlocks();
    $this->assertTrue($validationResult, 'Blocks in chain are valid');
    $lastBlock = $this->blockchainService->getStorageService()->getLastBlock();
    $this->assertInstanceOf(BlockchainBlockInterface::class, $lastBlock, 'Last block obtained');
    $foundLastBlock = $this->blockchainService->getStorageService()->loadByTimestampAndHash($lastBlock->getTimestamp(), $lastBlock->getPreviousHash());
    $this->assertInstanceOf(BlockchainBlockInterface::class, $foundLastBlock, 'Block found by timestamp and hash');
    $existsByTimestampAndHash = $this->blockchainService->getStorageService()->existsByTimestampAndHash($lastBlock->getTimestamp(), $lastBlock->getPreviousHash());
    $this->assertTrue($existsByTimestampAndHash, 'Defined existence by timestamp and hash');
    $blocks = $this->blockchainService->getStorageService()->getBlocksFrom($lastBlock, 100);
    $this->assertEmpty($blocks, 'No blocks loaded');
    $firstBlock = $this->blockchainService->getStorageService()->getFirstBlock();
    $this->assertInstanceOf(BlockchainBlockInterface::class, $firstBlock, 'First block obtained');
    $this->assertEquals(1, $firstBlock->id(), 'First block id is 1');
    $blocks = $this->blockchainService->getStorageService()->getBlocksFrom($firstBlock, 100, FALSE);
    $this->assertCount(6, $blocks, 'Loaded 6 blocks');
    $this->blockchainService->getConfigService()->setCurrentConfig('blockchain_block');
    $currentConfig = $this->blockchainService->getConfigService()->getCurrentConfig();
    $this->assertEquals('blockchain_block', $currentConfig->id(), 'Current config set to native.');
    $count = $this->blockchainService->getStorageService()->getBlockCount();
    $this->assertEmpty($count, 'No blocks in default storage');
  }

}
