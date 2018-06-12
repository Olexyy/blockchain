<?php

namespace Drupal\Tests\blockchain_emulation\Functional;

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
    // Execute count to emulation blockchain.
    $params = [];
    $this->blockchainService->getApiService()->addRequiredParams($params);
    $result = $this->blockchainService->getApiService()->execute($this->baseUrl . '/blockchain/api/emulation/count', $params);
    $code = $result->getStatusCode();
    $this->assertEquals(200, $code, 'Response ok');
    $count = $result->getCountParam();
    $this->assertEquals(5, $count, 'Returned 5 blocks');
  }

}
