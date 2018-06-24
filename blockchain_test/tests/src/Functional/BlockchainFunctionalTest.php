<?php

namespace Drupal\Tests\blockchain_emulation\Functional;


use Drupal\blockchain\Entity\BlockchainBlockInterface;
use Drupal\blockchain\Entity\BlockchainConfigInterface;
use Drupal\blockchain\Entity\BlockchainNodeInterface;
use Drupal\blockchain\Service\BlockchainApiServiceInterface;
use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\blockchain\Utils\BlockchainRequestInterface;
use Drupal\Tests\BrowserTestBase;
use GuzzleHttp\Client;

/**
 * Tests blockchain.
 *
 * @group blockchain
 */
class BlockchainEmulationFunctionalTestFunctionalTest extends BrowserTestBase {

  /**
   * Http client.
   *
   * @var Client
   */
  protected $httpClient;

  /**
   * Blockchain service.
   *
   * @var BlockchainServiceInterface
   */
  protected $blockchainService;

  /**
   * Blockchain API subscribe Url.
   *
   * @var string
   */
  protected $blockchainSubscribeUrl;

  /**
   * Blockchain API announce Url.
   *
   * @var string
   */
  protected $blockchainAnnounceUrl;

  /**
   * Blockchain API fetch Url.
   *
   * @var string
   */
  protected $blockchainFetchUrl;

  /**
   * Blockchain API fetch Url.
   *
   * @var string
   */
  protected $blockchainPullUrl;

  /**
   * Local ip.
   *
   * @var string
   */
  protected $localIp;

  /**
   * Local port.
   *
   * @var string
   */
  protected $localPort;

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
    $this->localIp = '127.0.0.1';
    $this->localPort = '80';
    $this->assertNotEmpty($this->baseUrl, 'Base url is set.');
    $this->blockchainAnnounceUrl = $this->baseUrl . BlockchainApiServiceInterface::API_ANNOUNCE;
    $this->blockchainSubscribeUrl = $this->baseUrl . BlockchainApiServiceInterface::API_SUBSCRIBE;
    $this->blockchainFetchUrl = $this->baseUrl . BlockchainApiServiceInterface::API_FETCH;
    $this->blockchainPullUrl = $this->baseUrl . BlockchainApiServiceInterface::API_PULL;
    $this->assertNotEmpty($this->blockchainSubscribeUrl, 'Blockchain subscribe API url is set.');
    $this->httpClient = $this->container->get('http_client');
    $this->assertInstanceOf(Client::class, $this->httpClient,
      'HTTP client instantiated.');
    $this->blockchainService = $this->container->get('blockchain.service');
    $this->assertInstanceOf(BlockchainServiceInterface::class, $this->blockchainService,
      'Blockchain service instantiated.');
    // Set test config.
    $this->blockchainService->getConfigService()->discoverBlockchainConfigs();
    $configs = $this->blockchainService->getConfigService()->getAll();
    $this->assertCount(2, $configs, '2 config created');
    $this->setCurrentConfig('blockchain_test_block');
    // Enable API.
    $this->blockchainService->getConfigService()->getCurrentConfig()->setType(BlockchainConfigInterface::TYPE_MULTIPLE)->save();
    $type = $this->blockchainService->getConfigService()->getCurrentConfig()->getType();
    $this->assertEquals($type, BlockchainConfigInterface::TYPE_MULTIPLE, 'Blockchain type is multiple');
    // Ensure none blocks in blockchain.
    $this->assertFalse($this->blockchainService->getStorageService()->anyBlock(), 'Any block returns false');
    $this->blockchainService->getStorageService()->getGenericBlock()->save();
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
    $this->assertEquals(5, $this->blockchainService->getStorageService()->getBlockCount(), 'Set 5 blocks to blockchain emulation.');
    // Attach self to node list.
    $blockchainNodeId = $this->blockchainService->getConfigService()->getCurrentConfig()->getNodeId();
    $blockchainNode = $this->blockchainService->getNodeService()->create($blockchainNodeId, $blockchainNodeId, $this->baseUrl, $this->localPort);
    $this->assertInstanceOf(BlockchainNodeInterface::class, $blockchainNode, 'Blockchain node created');
  }

  /**
   * Tests emulation storage API COUNT.
   */
  public function testEmulationStorageApiCount() {

    $result = $this->blockchainService->getApiService()->executeCount($this->baseUrl);
    $code = $result->getStatusCode();
    $this->assertEquals(200, $code, 'Response ok');
    $count = $result->getCountParam();
    $this->assertEquals(5, $count, 'Returned 5 blocks');
  }

  /**
   * Tests emulation storage API FETCH.
   */
  public function testEmulationStorageApiFetch() {

    $firstBlock = $this->blockchainService->getStorageService()->getFirstBlock();
    $blockchainNodeId = $this->blockchainService->getConfigService()->getCurrentConfig()->getNodeId();
    // Switch to legacy storage.
    $this->setCurrentConfig('blockchain_block');
    // Sync node id to be in list.
    $this->blockchainService->getConfigService()->getCurrentConfig()->setNodeId($blockchainNodeId)->save();
    // Fetch by params.
    $params = [
      BlockchainRequestInterface::PARAM_PREVIOUS_HASH => $firstBlock->getPreviousHash(),
      BlockchainRequestInterface::PARAM_TIMESTAMP => $firstBlock->getTimestamp(),
    ];
    $this->blockchainService->getApiService()->addRequiredParams($params);
    $params[BlockchainRequestInterface::PARAM_TYPE] = 'blockchain_test_block';
    $result = $this->blockchainService->getApiService()->execute($this->blockchainFetchUrl, $params);
    $code = $result->getStatusCode();
    $this->assertEquals(200, $code, 'Response ok');
    $this->assertEquals('Success', $result->getMessageParam(), 'Response message ok');
    $this->assertTrue($result->getExistsParam(), 'Block exists');
    $this->assertEquals('Block exists', $result->getDetailsParam(), 'Response details ok');
    $this->assertEquals(4, $result->getCountParam(), 'Returned count');
    // Execute FETCH to emulation blockchain that downgrades to COUNT.
    $params = [];
    $this->blockchainService->getApiService()->addRequiredParams($params);
    $params[BlockchainRequestInterface::PARAM_TYPE] = 'blockchain_test_block';
    $result = $this->blockchainService->getApiService()->execute($this->blockchainFetchUrl, $params);
    $code = $result->getStatusCode();
    $this->assertEquals(200, $code, 'Response ok');
    $exists = $result->getExistsParam();
    $this->assertFalse($exists, 'Block not exists');
    $count = $result->getCountParam();
    $this->assertEquals(5, $count, 'Returned total count');
  }

  /**
   * Tests emulation storage API PULL.
   */
  public function testEmulationStorageApiPull() {

    $firstBlock = $this->blockchainService->getStorageService()->getFirstBlock();
    $blockchainNodeId = $this->blockchainService->getConfigService()->getCurrentConfig()->getNodeId();
    // Switch to legacy storage.
    $this->setCurrentConfig('blockchain_block');
    // Sync node id to be in list.
    $this->blockchainService->getConfigService()->getCurrentConfig()->setNodeId($blockchainNodeId)->save();
    // PULL by params.
    $params = [
      BlockchainRequestInterface::PARAM_PREVIOUS_HASH => $firstBlock->getPreviousHash(),
      BlockchainRequestInterface::PARAM_TIMESTAMP => $firstBlock->getTimestamp(),
      BlockchainRequestInterface::PARAM_COUNT => 4,
    ];
    $this->blockchainService->getApiService()->addRequiredParams($params);
    $params[BlockchainRequestInterface::PARAM_TYPE] = 'blockchain_test_block';
    $result = $this->blockchainService->getApiService()->execute($this->blockchainPullUrl, $params);
    $code = $result->getStatusCode();
    $this->assertEquals(200, $code, 'Response ok');
    $exists = $result->getExistsParam();
    $this->assertTrue($exists, 'Block exists');
    $blocks = $result->getBlocksParam();
    $this->assertCount(4, $blocks, 'Returned 4 blocks');
    $instantiatedBlocks = [$firstBlock];
    foreach ($blocks as $key => $block) {
      $instantiatedBlocks[$key+1]= $this->blockchainService->getStorageService()->createFromArray($block);
      $this->assertInstanceOf(BlockchainBlockInterface::class, $instantiatedBlocks[$key+1], 'BLock import ok');
    }
    $this->assertCount(5, $instantiatedBlocks, 'Blocks collected');
    $valid = $this->blockchainService->getValidatorService()->validateBlocks($instantiatedBlocks);
    $this->assertTrue($valid, 'Collected blocks are valid');

    // Execute PULL from scratch with count param only.
    $params = [
      BlockchainRequestInterface::PARAM_COUNT => 5,
    ];
    $this->blockchainService->getApiService()->addRequiredParams($params);
    $params[BlockchainRequestInterface::PARAM_TYPE] = 'blockchain_test_block';
    $result = $this->blockchainService->getApiService()->execute($this->blockchainPullUrl, $params);
    $code = $result->getStatusCode();
    $this->assertEquals(200, $code, 'Response ok');
    $exists = $result->getExistsParam();
    $this->assertFalse($exists, 'Block not exists');
    $blocks = $result->getBlocksParam();
    $this->assertCount(5, $blocks, 'Returned 5 blocks');
    $instantiatedBlocks = [];
    foreach ($blocks as $block) {
      $instantiatedBlocks[]= $this->blockchainService->getStorageService()->createFromArray($block);
    }
    $this->assertCount(5, $instantiatedBlocks, 'Blocks collected');
    $valid = $this->blockchainService->getValidatorService()->validateBlocks($instantiatedBlocks);
    $this->assertTrue($valid, 'Collected blocks are valid');
    // Simulate batch PULL from scratch.
    $params = [
      BlockchainRequestInterface::PARAM_COUNT => 1,
    ];
    $this->blockchainService->getApiService()->addRequiredParams($params);
    $params[BlockchainRequestInterface::PARAM_TYPE] = 'blockchain_test_block';
    $result = $this->blockchainService->getApiService()->execute($this->blockchainPullUrl, $params);
    $code = $result->getStatusCode();
    $this->assertEquals(200, $code, 'Response ok');
    $exists = $result->getExistsParam();
    $this->assertFalse($exists, 'Block not exists');
    $blocks = $result->getBlocksParam();
    $this->assertCount(1, $blocks, 'Returned 1 block');
    $currentBlock = $this->blockchainService->getStorageService()->createFromArray(current($blocks));
    $syncBocks[]= $currentBlock;
    for ($i = 0; $i < 4; $i++) {
      $params = [
        BlockchainRequestInterface::PARAM_COUNT => 1,
        BlockchainRequestInterface::PARAM_PREVIOUS_HASH => $currentBlock->getPreviousHash(),
        BlockchainRequestInterface::PARAM_TIMESTAMP => $currentBlock->getTimestamp(),
      ];
      $this->blockchainService->getApiService()->addRequiredParams($params);
      $params[BlockchainRequestInterface::PARAM_TYPE] = 'blockchain_test_block';
      $result = $this->blockchainService->getApiService()->execute($this->blockchainPullUrl, $params);
      $code = $result->getStatusCode();
      $this->assertEquals(200, $code, 'Response ok');
      $exists = $result->getExistsParam();
      $this->assertTrue($exists, 'Block exists');
      $blocks = $result->getBlocksParam();
      $this->assertCount(1, $blocks, 'Returned 1 block');
      $currentBlock = $this->blockchainService->getStorageService()->createFromArray(current($blocks));
      $syncBocks[]= $currentBlock;
    }
    $this->assertCount(5, $instantiatedBlocks, 'Blocks collected');
    $valid = $this->blockchainService->getValidatorService()->validateBlocks($instantiatedBlocks);
    $this->assertTrue($valid, 'Collected blocks are valid');
  }

  /**
   * Helper to switch config and assert it.
   *
   * @param string $configId
   *   Id of config.
   */
  public function setCurrentConfig($configId) {

    $this->blockchainService->getConfigService()->setCurrentConfig($configId);
    $currentConfig = $this->blockchainService->getConfigService()->getCurrentConfig();
    $this->assertInstanceOf(BlockchainConfigInterface::class, $currentConfig, 'Current config set.');
    $this->assertEquals($configId, $currentConfig->id(), 'Current config set to '. $configId .'.');
  }

}
