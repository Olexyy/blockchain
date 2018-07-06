<?php

namespace Drupal\Tests\blockchain\Kernel;


use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\blockchain\Service\BlockchainTempStoreServiceInterface;
use Drupal\blockchain_test\Service\BlockchainTestServiceInterface;
use Drupal\Core\TempStore\SharedTempStore;
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
   * Blockchain test service.
   *
   * @var BlockchainTestServiceInterface
   */
  protected $blockchainTestService;

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = ['system', 'blockchain', 'blockchain_test'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {

    parent::setUp();
    $this->installConfig('blockchain');
    $this->installConfig('blockchain_test');
    $this->installEntitySchema('blockchain_block');
    $this->installEntitySchema('blockchain_test_block');
    $this->installEntitySchema('blockchain_node');
    $this->installEntitySchema('blockchain_config');
    $this->installSchema('system', ['key_value_expire']);
    $this->blockchainService = $this->container->get('blockchain.service');
    $this->assertInstanceOf(BlockchainServiceInterface::class, $this->blockchainService,
      'Blockchain service instantiated.');
    $this->blockchainTestService = $this->container->get('blockchain.test.service');
    $this->assertInstanceOf(BlockchainTestServiceInterface::class, $this->blockchainTestService,
      'Blockchain test service instantiated.');
    $this->blockchainTestService->setTestContext($this);
    $this->blockchainTestService->initConfigs();
    $this->blockchainTestService->setConfig('blockchain_block');
  }

  /**
   * Test temp store service.
   */
  public function testTempStore() {

    $tempStore = $this->blockchainService->getTempStoreService();
    $this->assertInstanceOf(BlockchainTempStoreServiceInterface::class, $tempStore, 'Tempstore serivce obtained.');
    $storage = $tempStore->getBlockStorage();
    $this->assertInstanceOf(SharedTempStore::class, $storage, 'Tempstore storage obtained.');
    $blocks = $tempStore->getAll();
    $this->assertEmpty($blocks, 'No blocks in tempstore yet.');
    $block = $this->blockchainService->getStorageService()->getGenericBlock();
    $tempStore->save($block);
    $blocks = $tempStore->getAll();
    $this->assertCount(1, $blocks, 'One block added to tempstore');

    $tempStore->deleteAll();
    $blocks = $tempStore->getAll();
    $this->assertEmpty($blocks, 'No blocks in tempstore already.');
  }

}
