<?php

namespace Drupal\blockchain_test\Service;


use Drupal\blockchain\Entity\BlockchainConfigInterface;
use Drupal\blockchain\Entity\BlockchainNodeInterface;
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
   * Sase url.
   *
   * @var string
   */
  protected $baseUrl;

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
   * {@inheritdoc}
   */
  public function setTestContext(TestCase $testContext, $baseUrl = NULL) {

    $this->testContext = $testContext;
    $this->baseUrl = $baseUrl;
  }

  /**
   * {@inheritdoc}
   */
  public function initConfigs($linked = TRUE) {

    $this->blockchainService->getConfigService()->discoverBlockchainConfigs();
    $configs = $this->blockchainService->getConfigService()->getAll();
    $this->testContext->assertCount(2, $configs, '2 config created');
    $blockchainNodeId = $this->blockchainService->getConfigService()->generateId();
    if ($linked) {
      foreach ($this->blockchainService->getConfigService()->getAll() as $blockchainConfig) {
        $blockchainConfig->setNodeId($blockchainNodeId)->save();
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function setConfig($configId) {

    $isSet = $this->blockchainService->getConfigService()->setCurrentConfig($configId);
    $this->testContext->assertTrue($isSet, 'Blockchain config is set.');
    $currentConfig = $this->blockchainService->getConfigService()->getCurrentConfig();
    $this->testContext->assertInstanceOf(BlockchainConfigInterface::class, $currentConfig, 'Current config set.');
    $this->testContext->assertEquals($configId, $currentConfig->id(), 'Current config setting confirmed.');
  }

  /**
   * {@inheritdoc}
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
                  ->toHash()
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

  /**
   * {@inheritdoc}
   */
  public function setBlockchainType($type) {

    $this->blockchainService->getConfigService()->getCurrentConfig()->setType($type)->save();
    $type = $this->blockchainService->getConfigService()->getCurrentConfig()->getType();
    $this->testContext->assertEquals($type, $type, 'Blockchain type is set.');
  }

  /**
   * {@inheritdoc}
   */
  public function createNode($baseUrl = NULL, BlockchainConfigInterface $blockchainConfig = NULL) {

    $baseUrl = ($baseUrl) ? $baseUrl : $this->baseUrl;
    $blockchainConfig = ($blockchainConfig) ? $blockchainConfig :
      $this->blockchainService->getConfigService()->getCurrentConfig();
    $blockchainNode = $this->blockchainService->getNodeService()->create(
      $blockchainConfig->id(),
      $blockchainConfig->getNodeId(),
      BlockchainNodeInterface::ADDRESS_SOURCE_CLIENT,
      $baseUrl
    );
    $this->testContext->assertInstanceOf(BlockchainNodeInterface::class, $blockchainNode, 'Blockchain node created');
  }

}
