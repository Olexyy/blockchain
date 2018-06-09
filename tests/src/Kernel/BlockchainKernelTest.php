<?php

namespace Drupal\Tests\blockchain\Kernel;

use Drupal\blockchain\Service\BlockchainConfigServiceInterface;
use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\blockchain_emulation\Service\BlockchainEmulationStorageServiceInterface;
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
   * Tests that default values are correctly translated to UUIDs in config.
   */
  public function testBlockchainService() {

    // Implement tests:
    //  - config;
    //  - nodes;
    //  - storage;
    $type = $this->blockchainService->getConfigService()->getBlockchainType();
    $this->assertEquals($type, BlockchainConfigServiceInterface::TYPE_SINGLE, 'Blockchain type is single');
  }

  /**
   * Test emulation storage.
   */
  public function testEmulationStorage() {

    $count = $this->blockchainEmulationStorage->getBlockCount();
    $this->assertEmpty($count, 'No blocks in storage');
  }

}
