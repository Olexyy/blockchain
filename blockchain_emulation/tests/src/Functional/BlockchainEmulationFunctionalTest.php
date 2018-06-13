<?php

namespace Drupal\Tests\blockchain_emulation\Functional;

use Drupal\blockchain\Entity\BlockchainBlock;
use Drupal\blockchain\Entity\BlockchainBlockInterface;
use Drupal\blockchain\Entity\BlockchainNodeInterface;
use Drupal\blockchain\Service\BlockchainApiServiceInterface;
use Drupal\blockchain\Service\BlockchainConfigServiceInterface;
use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\blockchain\Utils\BlockchainRequestInterface;
use Drupal\blockchain_emulation\Service\BlockchainEmulationStorageServiceInterface;
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
   * @var BlockchainEmulationStorageServiceInterface
   */
  protected $blockchainEmulationStorage;

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
  public static $modules = ['blockchain', 'blockchain_emulation'];

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
    $this->assertNotEmpty($this->blockchainSubscribeUrl, 'Blockchain subscribe API url is set.');
    $this->httpClient = $this->container->get('http_client');
    $this->assertInstanceOf(Client::class, $this->httpClient,
      'HTTP client instantiated.');
    $this->blockchainService = $this->container->get('blockchain.service');
    $this->assertInstanceOf(BlockchainServiceInterface::class, $this->blockchainService,
      'Blockchain service instantiated.');
    $this->blockchainEmulationStorage = $this->container->get('blockchain.emulation.storage');
    $this->assertInstanceOf(BlockchainEmulationStorageServiceInterface::class, $this->blockchainEmulationStorage,
      'Blockchain emulation storage instantiated.');

  }

  /**
   * Tests validation handler for blockchain API.
   */
  public function testEmulationStorageApiCalls() {

    // Enable API.
    $this->blockchainService->getConfigService()->setBlockchainType(BlockchainConfigServiceInterface::TYPE_MULTIPLE);
    $type = $this->blockchainService->getConfigService()->getBlockchainType();
    $this->assertEquals($type, BlockchainConfigServiceInterface::TYPE_MULTIPLE, 'Blockchain type is multiple');
    // Ensure none blocks in blockchain.
    $this->assertFalse($this->blockchainEmulationStorage->anyBlock(), 'Any block returns false');
    $this->blockchainEmulationStorage->setBlocks(5);
    $this->assertEquals(5, $this->blockchainEmulationStorage->getBlockCount(), 'Set 5 blocks to blockchain emulation.');
    // Attach self to node list.
    $blockchainNodeId = $this->blockchainService->getConfigService()->getBlockchainNodeId();
    $blockchainNode = $this->blockchainService->getNodeService()->create($blockchainNodeId, $blockchainNodeId, $this->baseUrl, $this->localPort);
    $this->assertInstanceOf(BlockchainNodeInterface::class, $blockchainNode, 'Blockchain node created');
    // Execute COUNT to emulation blockchain.
    $params = [];
    $this->blockchainService->getApiService()->addRequiredParams($params);
    $result = $this->blockchainService->getApiService()->execute($this->baseUrl . '/blockchain/api/emulation/count', $params);
    $code = $result->getStatusCode();
    $this->assertEquals(200, $code, 'Response ok');
    $count = $result->getCountParam();
    $this->assertEquals(5, $count, 'Returned 5 blocks');
    $firstBlock = $this->blockchainEmulationStorage->getBlockStorage()[0];
    // Execute FETCH to emulation blockchain.
    $params = [
      BlockchainRequestInterface::PARAM_PREVIOUS_HASH => $firstBlock->getPreviousHash(),
      BlockchainRequestInterface::PARAM_TIMESTAMP => $firstBlock->getTimestamp(),
    ];
    $this->blockchainService->getApiService()->addRequiredParams($params);
    $result = $this->blockchainService->getApiService()->execute($this->baseUrl . '/blockchain/api/emulation/fetch', $params);
    $code = $result->getStatusCode();
    $this->assertEquals(200, $code, 'Response ok');
    $exists = $result->getExistsParam();
    $this->assertTrue($exists, 'Block exists');
    $count = $result->getCountParam();
    $this->assertEquals(4, $count, 'Returned count');
    // Execute FETCH to emulation blockchain that downgrades to COUNT.
    $params = [];
    $this->blockchainService->getApiService()->addRequiredParams($params);
    $result = $this->blockchainService->getApiService()->execute($this->baseUrl . '/blockchain/api/emulation/fetch', $params);
    $code = $result->getStatusCode();
    $this->assertEquals(200, $code, 'Response ok');
    $exists = $result->getExistsParam();
    $this->assertFalse($exists, 'Block not exists');
    $count = $result->getCountParam();
    $this->assertEquals(5, $count, 'Returned total count');
    // Execute PULL to emulation blockchain.
    $params = [
      BlockchainRequestInterface::PARAM_PREVIOUS_HASH => $firstBlock->getPreviousHash(),
      BlockchainRequestInterface::PARAM_TIMESTAMP => $firstBlock->getTimestamp(),
      BlockchainRequestInterface::PARAM_COUNT => 4,
    ];
    $this->blockchainService->getApiService()->addRequiredParams($params);
    $result = $this->blockchainService->getApiService()->execute($this->baseUrl . '/blockchain/api/emulation/pull', $params);
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
    $result = $this->blockchainService->getApiService()->execute($this->baseUrl . '/blockchain/api/emulation/pull', $params);
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
  }

  /**
   * Writes data to console.
   *
   * @param $data
   */
  protected function consoleOut($data) {
    fwrite(STDOUT, print_r($data, TRUE));
  }
}

