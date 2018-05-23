<?php

namespace Drupal\Tests\blockchain\Functional;

use Drupal\blockchain\Entity\BlockchainNode;
use Drupal\blockchain\Entity\BlockchainNodeInterface;
use Drupal\blockchain\Service\BlockchainConfigServiceInterface;
use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\blockchain\Utils\BlockchainRequestInterface;
use Drupal\Tests\BrowserTestBase;
use GuzzleHttp\Client;

/**
 * Tests blockchain.
 *
 * @group blockchain
 */
class BlockchainFunctionalTest extends BrowserTestBase {

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
  public static $modules = ['blockchain'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {

    parent::setUp();
    $this->localIp = '127.0.0.1';
    $this->localPort = '80';
    $this->assertNotEmpty($this->baseUrl, 'Base url is set.');
    $this->blockchainSubscribeUrl = $this->baseUrl . '/blockchain/api/subscribe';
    $this->assertNotEmpty($this->blockchainSubscribeUrl, 'Blockchain subscribe API url is set.');
    $this->httpClient = $this->container->get('http_client');
    $this->assertInstanceOf(Client::class, $this->httpClient,
      'HTTP client instantiated.');
    $this->blockchainService = $this->container->get('blockchain.service');
    $this->assertInstanceOf(BlockchainServiceInterface::class, $this->blockchainService,
      'Blockchain service instantiated.');
  }

  /**
   * Tests that default values are correctly translated to UUIDs in config.
   */
  public function testBlockchainService() {

    // Cover method checking.
    $this->drupalGet($this->blockchainSubscribeUrl);
    $this->assertEquals(400, $this->getSession()->getStatusCode());
    $this->assertContains('{"message":"Bad request","details":"Incorrect method."}', $this->getSession()->getPage()->getContent());
    // Blockchain id is generated on first request, lets check it.
    $blockchainId = $this->blockchainService->getConfigService()->getBlockchainId();
    $this->assertNotEmpty($blockchainId, 'Blockchain id is generated.');
    $this->assertEquals($blockchainId, $this->blockchainService->getConfigService()->getBlockchainId(),
      'Blockchain id is not regenerated on second call');
    // Blockchain node id is generated on first request, lets check it.
    $blockchainNodeId = $this->blockchainService->getConfigService()->getBlockchainNodeId();
    $this->assertNotEmpty($blockchainNodeId, 'Blockchain node id is generated.');
    $this->assertEquals($blockchainNodeId, $this->blockchainService->getConfigService()->getBlockchainNodeId(),
      'Blockchain id is not regenerated on second call');
    // Ensure Blockchain type is 'single'.
    $type = $this->blockchainService->getConfigService()->getBlockchainType();
    $this->assertEquals($type, BlockchainConfigServiceInterface::TYPE_SINGLE, 'Blockchain type is single');
    // Ensure Blockchain 'auth' is false by default.
    $auth = $this->blockchainService->getConfigService()->isBlockchainAuth();
    $this->assertFalse($auth, 'Blockchain auth is disabled');
    // Cover API is restricted for 'single' type. Request is normal.
    $response = $this->blockchainService->getApiService()->executeSubscribe($this->baseUrl);
    $this->assertEquals(403, $response->getStatusCode());
    $this->assertEquals('Forbidden', $response->getMessageParam());
    $this->assertEquals('Access to this resource is restricted.', $response->getDetailsParam());
    // Set and ensure blockchain type is 'multiple'.
    $this->blockchainService->getConfigService()->setBlockchainType(BlockchainConfigServiceInterface::TYPE_MULTIPLE);
    $type = $this->blockchainService->getConfigService()->getBlockchainType();
    $this->assertEquals($type, BlockchainConfigServiceInterface::TYPE_MULTIPLE, 'Blockchain type is multiple');
    // Try to access with no 'self' param.
    $response = $this->blockchainService->getApiService()->execute($this->blockchainSubscribeUrl, []);
    $this->assertEquals(400, $response->getStatusCode());
    $this->assertEquals('Bad request', $response->getMessageParam());
    $this->assertEquals('No self param.', $response->getDetailsParam());
    // Enable auth.
    $this->blockchainService->getConfigService()->setBlockchainAuth(TRUE);
    $auth = $this->blockchainService->getConfigService()->isBlockchainAuth();
    $this->assertTrue($auth, 'Blockchain auth is enabled');
    // Cover API is restricted for non 'auth' request.
    $response = $this->blockchainService->getApiService()->execute($this->blockchainSubscribeUrl, [
      BlockchainRequestInterface::PARAM_SELF => $blockchainNodeId,
    ]);
    $this->assertEquals(401, $response->getStatusCode());
    $this->assertEquals('Unauthorized', $response->getMessageParam());
    $this->assertEquals('Auth token required.', $response->getDetailsParam());
    // Cover API is restricted for invalid 'auth' request.
    $response = $this->blockchainService->getApiService()->execute($this->blockchainSubscribeUrl, [
      BlockchainRequestInterface::PARAM_SELF => $blockchainNodeId,
      BlockchainRequestInterface::PARAM_AUTH => 'INVALIDAUTHPARAM',
    ]);
    $this->assertEquals(401, $response->getStatusCode());
    $this->assertEquals('Unauthorized', $response->getMessageParam());
    $this->assertEquals('Auth token invalid.', $response->getDetailsParam());
    // TODO test not subscribed yet test case.
    // Ensure we have blacklist filter mode.
    $blockchainFilterType = $this->blockchainService->getConfigService()->getBlockchainFilterType();
    $this->assertEquals($blockchainFilterType, BlockchainConfigServiceInterface::FILTER_TYPE_BLACKLIST, 'Blockchain filter type is blacklist');
    $blacklist = $this->blockchainService->getConfigService()->getBlockchainFilterList();
    $this->assertEmpty($blacklist,'Blockchain blacklist is empty');
    $this->blockchainService->getConfigService()->setBlockchainFilterListAsArray($this->getBlacklist());
    // Ensure we included our ip in black list.
    $blacklist = $this->blockchainService->getConfigService()->getBlockchainFilterListAsArray();
    $this->assertEquals($this->getBlacklist(), $blacklist, 'Blacklist is equal to expected.');
    // Generate valid token, so we cover check for blacklist.
    $authToken = $this->blockchainService->getConfigService()->tokenGenerate();
    $response = $this->blockchainService->getApiService()->execute($this->blockchainSubscribeUrl, [
      BlockchainRequestInterface::PARAM_SELF => $blockchainNodeId,
      BlockchainRequestInterface::PARAM_AUTH => $authToken,
    ]);
    $this->assertEquals(403, $response->getStatusCode());
    $this->assertEquals('Forbidden', $response->getMessageParam());
    $this->assertEquals('You are forbidden to access this resource.', $response->getDetailsParam());
    // Ensure we have whitelist filter mode.
    $this->blockchainService->getConfigService()->setBlockchainFilterType(BlockchainConfigServiceInterface::FILTER_TYPE_WHITELIST);
    $blockchainFilterType = $this->blockchainService->getConfigService()->getBlockchainFilterType();
    $this->assertEquals($blockchainFilterType, BlockchainConfigServiceInterface::FILTER_TYPE_WHITELIST, 'Blockchain filter type is whitelist');
    // Ensure put ip is not in whitelist.
    $this->blockchainService->getConfigService()->setBlockchainFilterListAsArray($this->getWhitelist());
    $whitelist = $this->blockchainService->getConfigService()->getBlockchainFilterList();
    $this->assertNotContains($this->localIp, $whitelist, 'Whitelist does not have local ip address.');
    // Cover check for whitelist.
    $response = $this->blockchainService->getApiService()->execute($this->blockchainSubscribeUrl, [
      BlockchainRequestInterface::PARAM_SELF => $blockchainNodeId,
      BlockchainRequestInterface::PARAM_AUTH => $authToken,
    ]);
    $this->assertEquals(403, $response->getStatusCode());
    $this->assertEquals('Forbidden', $response->getMessageParam());
    $this->assertEquals('You are forbidden to access this resource.', $response->getDetailsParam());
    // Lets reset this for further testing.
    $this->blockchainService->getConfigService()->setBlockchainFilterListAsArray([]);
    $whitelist = $this->blockchainService->getConfigService()->getBlockchainFilterList();
    $this->assertEmpty($whitelist, 'Whitelist is empty.');
    // Lets focus on Blockchain nodes. Ensure we have any.
    $blockchainNodeExists = $this->blockchainService->getNodeService()->exists($blockchainNodeId);
    $this->assertFalse($blockchainNodeExists, 'Blockchain node not exists in list');
    $nodeCount = count($this->blockchainService->getNodeService()->getList());
    $this->assertEmpty($nodeCount, 'Blockchain node list empty');
    // Try to create one. Ensure list is not empty.
    $blockchainNode = $this->blockchainService->getNodeService()->create($blockchainNodeId, $blockchainNodeId, $this->localIp, $this->localPort);
    $this->assertInstanceOf(BlockchainNodeInterface::class, $blockchainNode, 'Blockchain node created');
    $blockchainNodeExists = $this->blockchainService->getNodeService()->exists($blockchainNodeId);
    $this->assertTrue($blockchainNodeExists, 'Blockchain node exists in list');
    $nodeCount = count($this->blockchainService->getNodeService()->getList());
    $this->assertEquals(1, $nodeCount, 'Blockchain node list not empty');
    // Cover 'already exists' use case. Use native request method here.
    $response = $this->blockchainService->getApiService()->executeSubscribe($this->baseUrl);
    $this->assertEquals(406, $response->getStatusCode());
    $this->assertEquals('Not acceptable', $response->getMessageParam());
    $this->assertEquals('Already in list.', $response->getDetailsParam());
    // Finally delete node and try to create it via subscribe with response 200.
    $this->blockchainService->getNodeService()->delete($blockchainNode);
    $blockchainNodeExists = $this->blockchainService->getNodeService()->exists($blockchainNodeId);
    $this->assertFalse($blockchainNodeExists, 'Blockchain node not exists in list');
    $nodeCount = $this->blockchainService->getNodeService()->getList();
    $this->assertEmpty($nodeCount, 'Blockchain node list empty');
    $response = $this->blockchainService->getApiService()->executeSubscribe($this->baseUrl);
    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals('Success', $response->getMessageParam());
    $this->assertEquals('Added to list.', $response->getDetailsParam());
    $blockchainNodeExists = $this->blockchainService->getNodeService()->exists($blockchainNodeId);
    $this->assertTrue($blockchainNodeExists, 'Blockchain node exists in list');
    $testLoad = $this->blockchainService->getNodeService()->load($blockchainNodeId);
    $this->assertInstanceOf(BlockchainNodeInterface::class, $testLoad, 'Blockchain node loaded');
  }

  /**
   * Getter for ips.
   *
   * @return string[]
   *   Array of ips including self.
   */
  protected function getBlacklist() {

    return ['127.0.0.1', '127.0.0.3', '127.0.0.5'];
  }

  /**
   * Getter for ips.
   *
   * @return string[]
   *   Array of ips excluding self.
   */
  protected function getWhitelist() {

    return ['127.0.0.2', '127.0.0.4', '127.0.0.6'];
  }

}
