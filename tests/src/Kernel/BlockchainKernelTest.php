<?php

namespace Drupal\Tests\blockchain\Kernel;

use Drupal\blockchain\Service\BlockchainConfigServiceInterface;
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
  public static $modules = ['blockchain'];

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

}
