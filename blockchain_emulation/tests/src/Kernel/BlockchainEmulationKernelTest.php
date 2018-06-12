<?php

namespace Drupal\Tests\blockchain\Kernel;

use Drupal\blockchain\Entity\BlockchainBlockInterface;
use Drupal\blockchain\Service\BlockchainConfigServiceInterface;
use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\blockchain\Utils\Util;
use Drupal\blockchain_emulation\Service\BlockchainEmulationStorageServiceInterface;
use Drupal\KernelTests\KernelTestBase;

/**
 * Tests blockchain.
 *
 * @group blockchain
 */
class BlockchainEmulationKernelTest extends KernelTestBase {

  /**
   * Blockchain service.
   *
   * @var BlockchainServiceInterface
   */
  protected $blockchainService;

  /**
   * Emulation storage.
   *
   * @var BlockchainEmulationStorageServiceInterface
   */
  protected $blockchainEmulationStorage;

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = ['blockchain', 'blockchain_emulation'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {

    parent::setUp();
    $this->installConfig('blockchain');
    $this->installEntitySchema('blockchain_block');
    $this->installEntitySchema('blockchain_node');
    $this->blockchainService = $this->container->get('blockchain.service');
    $this->assertInstanceOf(BlockchainServiceInterface::class, $this->blockchainService,
      'Blockchain service instantiated.');
    $this->blockchainEmulationStorage = $this->container->get('blockchain.emulation.storage');
    $this->assertInstanceOf(BlockchainEmulationStorageServiceInterface::class, $this->blockchainEmulationStorage,
      'Blockchain emulation storage instantiated.');
  }

  /**
   * Test emulation storage.
   */
  public function testEmulationStorage() {

    $count = $this->blockchainEmulationStorage->getBlockCount();
    $this->assertEmpty($count, 'No blocks in storage');
    $this->assertFalse($this->blockchainEmulationStorage->anyBlock(), 'Any block not found');
    $this->blockchainEmulationStorage->setBlocks(5);
    $count = $this->blockchainEmulationStorage->getBlockCount();
    $this->assertTrue($this->blockchainEmulationStorage->anyBlock(), 'Any block found');
    $this->assertEquals(5, $count, 'Set count of blocks to 5');
    $this->blockchainEmulationStorage->setBlocks(2);
    $count = $this->blockchainEmulationStorage->getBlockCount();
    $this->assertEquals(2, $count, 'Set count of blocks to 2');
    $this->blockchainEmulationStorage->setBlocks(20);
    $count = $this->blockchainEmulationStorage->getBlockCount();
    $this->assertEquals(20, $count, 'Set count of blocks to 20');
    $checkLast = $this->blockchainService->getValidatorService()->blockIsValid($this->blockchainEmulationStorage->getLastBlock());
    $this->assertTrue($checkLast, 'Last block is valid');
    $validationResult = $this->blockchainEmulationStorage->checkBlocks();
    $this->assertTrue($validationResult, 'Blocks in chain are valid');
    $lastBlock = $this->blockchainEmulationStorage->getLastBlock();
    $this->assertInstanceOf(BlockchainBlockInterface::class, $lastBlock, 'Last block obtained');
    $foundLastBlock = $this->blockchainEmulationStorage->loadByTimestampAndHash($lastBlock->getTimestamp(), $lastBlock->getPreviousHash());
    $this->assertInstanceOf(BlockchainBlockInterface::class, $foundLastBlock, 'Block found by timestamp and hash');
    $existsByTimestampAndHash = $this->blockchainEmulationStorage->existsByTimestampAndHash($lastBlock->getTimestamp(), $lastBlock->getPreviousHash());
    $this->assertTrue($existsByTimestampAndHash, 'Defined existence by timestamp and hash');
    $blocks = $this->blockchainEmulationStorage->getBlocksFrom($lastBlock, 100);
    $this->assertEmpty($blocks, 'No blocks loaded');
    $firstBlock = $this->blockchainEmulationStorage->getBlockStorage()[0];
    $blocks = $this->blockchainEmulationStorage->getBlocksFrom($firstBlock, 100);
    $this->assertCount(19, $blocks, 'Loaded 19 blocks');
  }

}
