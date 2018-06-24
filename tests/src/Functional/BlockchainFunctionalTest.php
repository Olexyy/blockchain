<?php

namespace Drupal\Tests\blockchain\Functional;

use Drupal\blockchain\Entity\BlockchainBlockInterface;
use Drupal\blockchain\Entity\BlockchainConfigInterface;
use Drupal\blockchain\Entity\BlockchainNodeInterface;
use Drupal\blockchain\Plugin\BlockchainAuthManager;
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
  public static $modules = ['blockchain'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {

    parent::setUp();
    $this->localIp = '127.0.0.1';
    $this->localPort = '80';
    $this->assertEquals($this->baseUrl, 'http://et_legis.loc','Base url is set.');
    $this->blockchainAnnounceUrl = $this->baseUrl . BlockchainApiServiceInterface::API_ANNOUNCE;
    $this->blockchainSubscribeUrl = $this->baseUrl . BlockchainApiServiceInterface::API_SUBSCRIBE;
    $this->assertNotEmpty($this->blockchainSubscribeUrl, 'Blockchain subscribe API url is set.');
    $this->httpClient = $this->container->get('http_client');
    $this->assertInstanceOf(Client::class, $this->httpClient,
      'HTTP client instantiated.');
    $this->blockchainService = $this->container->get('blockchain.service');
    $this->assertInstanceOf(BlockchainServiceInterface::class, $this->blockchainService,
      'Blockchain service instantiated.');
    $count = $this->blockchainService->getConfigService()->discoverBlockchainConfigs();
    $this->assertEquals(1, $count, 'Discovered one config.');
    $blockchainConfigs = $this->blockchainService->getConfigService()->getAll();
    $this->assertCount(1, $blockchainConfigs, 'Exists one config.');
    $isSet = $this->blockchainService->getConfigService()->setCurrentConfig(current($blockchainConfigs)->id());
    $this->assertTrue($isSet, 'Current config is set.');
  }

  /**
   * Tests validation handler for blockchain API.
   */
  public function testBlockchainApiValidation() {

    // Cover method checking.
    $this->drupalGet($this->blockchainSubscribeUrl);
    $this->assertEquals(400, $this->getSession()->getStatusCode());
    $this->assertContains('{"message":"Bad request","details":"Incorrect method."}', $this->getSession()->getPage()->getContent());
    // Cover protocol schema.
    $allowNotSecure = $this->blockchainService->getConfigService()->getCurrentConfig()->getAllowNotSecure();
    $this->assertTrue($allowNotSecure, 'Secure protocol not required by default');
    // Try to access with no type param.
    $response = $this->blockchainService->getApiService()->execute($this->blockchainSubscribeUrl, []);
    $this->assertEquals(400, $response->getStatusCode());
    $this->assertEquals('Bad request', $response->getMessageParam());
    $this->assertEquals('Missing type param.', $response->getDetailsParam());
    $this->blockchainService->getConfigService()->getCurrentConfig()->setAllowNotSecure(FALSE)->save();
    $allowNotSecure = $this->blockchainService->getConfigService()->getCurrentConfig()->getAllowNotSecure();
    $this->assertFalse($allowNotSecure, 'Secure protocol is required now');
    // Try to access with invalid type.
    $response = $this->blockchainService->getApiService()->execute($this->blockchainSubscribeUrl, [
      BlockchainRequestInterface::PARAM_TYPE => 'blockchain_non_existent',
    ]);
    $this->assertEquals(400, $response->getStatusCode());
    $this->assertEquals('Bad request', $response->getMessageParam());
    $this->assertEquals('Invalid type param.', $response->getDetailsParam());
    // Try to access with no incorrect protocol.
    $response = $this->blockchainService->getApiService()->execute($this->blockchainSubscribeUrl, [
      BlockchainRequestInterface::PARAM_TYPE => 'blockchain_block',
    ]);
    $this->assertEquals(400, $response->getStatusCode());
    $this->assertEquals('Bad request', $response->getMessageParam());
    $this->assertEquals('Incorrect protocol.', $response->getDetailsParam());
    $this->blockchainService->getConfigService()->getCurrentConfig()->setAllowNotSecure(TRUE)->save();
    $allowNotSecure = $this->blockchainService->getConfigService()->getCurrentConfig()->getAllowNotSecure();
    $this->assertTrue($allowNotSecure, 'Secure protocol not required again');
    // Blockchain id is generated on first request, lets check it.
    $blockchainId = $this->blockchainService->getConfigService()->getCurrentConfig()->getBlockchainId();
    $this->assertNotEmpty($blockchainId, 'Blockchain id is generated.');
    $this->assertEquals($blockchainId, $this->blockchainService->getConfigService()->getCurrentConfig()->getBlockchainId(),
      'Blockchain id is not regenerated on second call');
    // Blockchain node id is generated on first request, lets check it.
    $blockchainNodeId = $this->blockchainService->getConfigService()->getCurrentConfig()->getNodeId();
    $this->assertNotEmpty($blockchainNodeId, 'Blockchain node id is generated.');
    $this->assertEquals($blockchainNodeId, $this->blockchainService->getConfigService()->getCurrentConfig()->getNodeId(),
      'Blockchain id is not regenerated on second call');
    // Ensure Blockchain type is 'single'.
    $type = $this->blockchainService->getConfigService()->getCurrentConfig()->getType();
    $this->assertEquals($type, BlockchainConfigInterface::TYPE_SINGLE, 'Blockchain type is single');
    // Ensure Blockchain 'auth' is false by default.
    $auth = $this->blockchainService->getConfigService()->getCurrentConfig()->getAuth();
    $this->assertEquals(BlockchainAuthManager::DEFAULT_PLUGIN, $auth, 'Blockchain set to none');
    // Cover API is restricted for 'single' type. Request is normal.
    $response = $this->blockchainService->getApiService()->executeSubscribe($this->baseUrl);
    $this->assertEquals('Access to this resource is restricted.', $response->getDetailsParam());
    $this->assertEquals('Forbidden', $response->getMessageParam());
    $this->assertEquals(403, $response->getStatusCode());
    // Set and ensure blockchain type is 'multiple'.
    $this->blockchainService->getConfigService()->getCurrentConfig()->setType(BlockchainConfigInterface::TYPE_MULTIPLE)->save();
    $type = $this->blockchainService->getConfigService()->getCurrentConfig()->getType();
    $this->assertEquals($type, BlockchainConfigInterface::TYPE_MULTIPLE, 'Blockchain type is multiple');
    // Try to access with no 'self' param.
    $response = $this->blockchainService->getApiService()->execute($this->blockchainSubscribeUrl, [
      BlockchainRequestInterface::PARAM_TYPE => 'blockchain_block',
    ]);
    $this->assertEquals(400, $response->getStatusCode());
    $this->assertEquals('Bad request', $response->getMessageParam());
    $this->assertEquals('No self param.', $response->getDetailsParam());
    // Generate valid token
    $authToken = $this->blockchainService->getConfigService()->tokenGenerate();
    // Enable auth.
    $this->blockchainService->getConfigService()->getCurrentConfig()->setAuth('shared_key')->save();
    $auth = $this->blockchainService->getConfigService()->getCurrentConfig()->getAuth();
    $this->assertEquals('shared_key', $auth, 'Blockchain auth is enabled');
    // Cover API is restricted for non 'auth' request.
    $response = $this->blockchainService->getApiService()->execute($this->blockchainSubscribeUrl, [
      BlockchainRequestInterface::PARAM_SELF => $blockchainNodeId,
      BlockchainRequestInterface::PARAM_TYPE => 'blockchain_block',
    ]);
    $this->assertEquals(401, $response->getStatusCode());
    $this->assertEquals('Unauthorized', $response->getMessageParam());
    $this->assertEquals('Auth token invalid.', $response->getDetailsParam());
    // Cover API is restricted for invalid 'auth' request.
    $response = $this->blockchainService->getApiService()->execute($this->blockchainSubscribeUrl, [
      BlockchainRequestInterface::PARAM_SELF => $blockchainNodeId,
      BlockchainRequestInterface::PARAM_AUTH => 'INVALIDAUTHPARAM',
      BlockchainRequestInterface::PARAM_TYPE => 'blockchain_block',
    ]);
    $this->assertEquals(401, $response->getStatusCode());
    $this->assertEquals('Unauthorized', $response->getMessageParam());
    $this->assertEquals('Auth token invalid.', $response->getDetailsParam());
    // Test not subscribed yet test case.
    $response = $this->blockchainService->getApiService()->execute($this->blockchainAnnounceUrl, [
      BlockchainRequestInterface::PARAM_SELF => $blockchainNodeId,
      BlockchainRequestInterface::PARAM_AUTH => $authToken,
      BlockchainRequestInterface::PARAM_TYPE => 'blockchain_block',
    ]);
    $this->assertEquals(401, $response->getStatusCode());
    $this->assertEquals('Unauthorized', $response->getMessageParam());
    $this->assertEquals('Not subscribed yet.', $response->getDetailsParam());
    // Ensure we have blacklist filter mode.
    $blockchainFilterType = $this->blockchainService->getConfigService()->getCurrentConfig()->getFilterType();
    $this->assertEquals($blockchainFilterType, BlockchainConfigInterface::FILTER_TYPE_BLACKLIST, 'Blockchain filter type is blacklist');
    $blacklist = $this->blockchainService->getConfigService()->getCurrentConfig()->getFilterList();
    $this->assertEmpty($blacklist,'Blockchain blacklist is empty');
    $this->blockchainService->getConfigService()->getCurrentConfig()->setBlockchainFilterListAsArray($this->getBlacklist())->save();
    // Ensure we included our ip in black list.
    $blacklist = $this->blockchainService->getConfigService()->getCurrentConfig()->getBlockchainFilterListAsArray();
    $this->assertEquals($this->getBlacklist(), $blacklist, 'Blacklist is equal to expected.');
    // Cover check for blacklist.
    $response = $this->blockchainService->getApiService()->execute($this->blockchainSubscribeUrl, [
      BlockchainRequestInterface::PARAM_SELF => $blockchainNodeId,
      BlockchainRequestInterface::PARAM_AUTH => $authToken,
      BlockchainRequestInterface::PARAM_TYPE => 'blockchain_block',
    ]);
    $this->assertEquals(403, $response->getStatusCode());
    $this->assertEquals('Forbidden', $response->getMessageParam());
    $this->assertEquals('You are forbidden to access this resource.', $response->getDetailsParam());
    // Ensure we have whitelist filter mode.
    $this->blockchainService->getConfigService()->getCurrentConfig()->setFilterType(BlockchainConfigInterface::FILTER_TYPE_WHITELIST)->save();
    $blockchainFilterType = $this->blockchainService->getConfigService()->getCurrentConfig()->getFilterType();
    $this->assertEquals($blockchainFilterType, BlockchainConfigInterface::FILTER_TYPE_WHITELIST, 'Blockchain filter type is whitelist');
    // Ensure put ip is not in whitelist.
    $this->blockchainService->getConfigService()->getCurrentConfig()->setBlockchainFilterListAsArray($this->getWhitelist())->save();
    $whitelist = $this->blockchainService->getConfigService()->getCurrentConfig()->getFilterList();
    $this->assertNotContains($this->localIp, $whitelist, 'Whitelist does not have local ip address.');
    // Cover check for whitelist.
    $response = $this->blockchainService->getApiService()->execute($this->blockchainSubscribeUrl, [
      BlockchainRequestInterface::PARAM_SELF => $blockchainNodeId,
      BlockchainRequestInterface::PARAM_AUTH => $authToken,
      BlockchainRequestInterface::PARAM_TYPE => 'blockchain_block',
    ]);
    $this->assertEquals(403, $response->getStatusCode());
    $this->assertEquals('Forbidden', $response->getMessageParam());
    $this->assertEquals('You are forbidden to access this resource.', $response->getDetailsParam());
    // Lets reset this for further testing.
    $this->blockchainService->getConfigService()->getCurrentConfig()->setBlockchainFilterListAsArray([])->save();
    $whitelist = $this->blockchainService->getConfigService()->getCurrentConfig()->getFilterList();
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
    $nodeCount = $this->blockchainService->getNodeService()->getList();
    $this->assertCount(1, $nodeCount, 'Blockchain node list not empty');
    // Cover 'already exists' use case. Use native request method here.
    $response = $this->blockchainService->getApiService()->executeSubscribe($this->baseUrl);
    $this->assertEquals(406, $response->getStatusCode());
    $this->assertEquals('Not acceptable', $response->getMessageParam());
    $this->assertEquals('Already in list.', $response->getDetailsParam());
    // Delete node.
    $this->blockchainService->getNodeService()->delete($blockchainNode);
    $blockchainNodeExists = $this->blockchainService->getNodeService()->exists($blockchainNodeId);
    $this->assertFalse($blockchainNodeExists, 'Blockchain node not exists in list');
    $nodeCount = $this->blockchainService->getNodeService()->getList();
    $this->assertEmpty($nodeCount, 'Blockchain node list empty');
    // TODO SUCCESS CASE.
    $response = $this->blockchainService->getApiService()->executeSubscribe($this->baseUrl);
    $this->assertEquals(200, $response->getStatusCode(), 'Subscribed');
  }

  /**
   * Tests that default values are correctly translated to UUIDs in config.
   */
  public function testBlockchainServiceSubscribe() {

    // Enable API.
    $this->blockchainService->getConfigService()->getCurrentConfig()->setType(BlockchainConfigInterface::TYPE_MULTIPLE)->save();
    $type = $this->blockchainService->getConfigService()->getCurrentConfig()->getType();
    $this->assertEquals($type, BlockchainConfigInterface::TYPE_MULTIPLE, 'Blockchain type is multiple');
    // Test subscribe method.
    $response = $this->blockchainService->getApiService()->executeSubscribe($this->baseUrl);
    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals('Success', $response->getMessageParam());
    $this->assertEquals('Added to list.', $response->getDetailsParam());
    $blockchainNodeId = $this->blockchainService->getConfigService()->getCurrentConfig()->getNodeId();
    $blockchainNodeExists = $this->blockchainService->getNodeService()->exists($blockchainNodeId);
    $this->assertTrue($blockchainNodeExists, 'Blockchain node exists in list');
    $testLoad = $this->blockchainService->getNodeService()->load($blockchainNodeId);
    $this->assertInstanceOf(BlockchainNodeInterface::class, $testLoad, 'Blockchain node loaded');
    $testLoad = $this->blockchainService->getNodeService()->load('NON_EXISTENT');
    $this->assertEmpty($testLoad, 'Non existent Blockchain node not loaded');
    $testLoad = $this->blockchainService->getNodeService()->exists('NON_EXISTENT');
    $this->assertFalse($testLoad, 'Non existent Blockchain node not loaded');
  }

  /**
   * Tests that default values are correctly translated to UUIDs in config.
   */
  public function testBlockchainServiceAnnounce() {

    // Enable API.
    $this->blockchainService->getConfigService()->getCurrentConfig()->setType(BlockchainConfigInterface::TYPE_MULTIPLE)->save();
    $type = $this->blockchainService->getConfigService()->getCurrentConfig()->getType();
    $this->assertEquals($type, BlockchainConfigInterface::TYPE_MULTIPLE, 'Blockchain type is multiple');
    // Ensure none blocks in blockchain.
    $this->assertFalse($this->blockchainService->getStorageService()->anyBlock(), 'Any block returns false');
    $blockCount = $this->blockchainService->getStorageService()->getBlockCount();
    $this->assertEmpty($blockCount, 'None blocks in storage yet.');
    // Create generic block and add it to blockchain.
    $genericBlock = $this->blockchainService->getStorageService()->getGenericBlock();
    $this->assertInstanceOf(BlockchainBlockInterface::class, $genericBlock,'Generic block created.');
    $this->blockchainService->getStorageService()->save($genericBlock);
    $blockCount = $this->blockchainService->getStorageService()->getBlockCount();
    $this->assertTrue($this->blockchainService->getStorageService()->anyBlock(), 'Any block returns true');
    $this->assertNotEmpty($blockCount, 'Generic block added to storage.');
    $lastBlock = $this->blockchainService->getStorageService()->getLastBlock();
    $this->assertInstanceOf(BlockchainBlockInterface::class, $lastBlock, 'Last block obtained');
    $this->assertEquals(1, $lastBlock->id(), 'Last block id obtained');
    $blockByTimestampAndHash = $this->blockchainService->getStorageService()->loadByTimestampAndHash(
      $lastBlock->getTimestamp(), $lastBlock->getPreviousHash()
    );
    $this->assertInstanceOf(BlockchainBlockInterface::class, $blockByTimestampAndHash, 'Block by Timestamp and previous hash block obtained');
    // Ensure no nodes in list yet.
    $nodesCount = $this->blockchainService->getNodeService()->getCount();
    $this->assertEmpty($nodesCount, 'None blockchain nodes in list yet.');
    $announceCount = $this->blockchainService->getApiService()->executeAnnounce([
      BlockchainRequestInterface::PARAM_COUNT => $this->blockchainService->getStorageService()->getBlockCount()
    ]);
    $this->assertEmpty($announceCount, 'Announce was related to none nodes.');
    // Set announce handling to CRON (no immediate) processing.
    $announceManagement = $this->blockchainService->getConfigService()->getCurrentConfig()->getAnnounceManagement();
    $this->assertEquals(BlockchainConfigInterface::ANNOUNCE_MANAGEMENT_IMMEDIATE, $announceManagement, 'Announce management is immediate.');
    $this->blockchainService->getConfigService()->getCurrentConfig()->setAnnounceManagement(BlockchainConfigInterface::ANNOUNCE_MANAGEMENT_CRON)->save();
    $announceManagement = $this->blockchainService->getConfigService()->getCurrentConfig()->getAnnounceManagement();
    $this->assertEquals(BlockchainConfigInterface::ANNOUNCE_MANAGEMENT_CRON, $announceManagement, 'Announce management set to CRON handled.');
    // Attach self to node list.
    $blockchainNodeId = $this->blockchainService->getConfigService()->getCurrentConfig()->getNodeId();
    $blockchainNode = $this->blockchainService->getNodeService()->create($blockchainNodeId, $blockchainNodeId, $this->baseUrl);
    $this->assertInstanceOf(BlockchainNodeInterface::class, $blockchainNode, 'Blockchain node created');
    $blockchainNodeExists = $this->blockchainService->getNodeService()->exists($blockchainNodeId);
    $this->assertTrue($blockchainNodeExists, 'Blockchain node exists in list');
    $nodeCount = $this->blockchainService->getNodeService()->getList();
    $this->assertCount(1, $nodeCount, 'Blockchain node list not empty');
    // Repeat announce and ensure it was passed to self as node.
    $announceCount = $this->blockchainService->getApiService()->executeAnnounce([
      BlockchainRequestInterface::PARAM_COUNT => $this->blockchainService->getStorageService()->getBlockCount()
    ]);
    $this->assertCount(1, $announceCount, 'Announce was related to one node.');
    $this->assertEquals(406, current($announceCount)->getStatusCode(), 'Status code for announce response is 406.');
    $processedAnnounces = $this->blockchainService->getQueueService()->doAnnounceHandling();
    // Ensure no announces processed as it was 406 (Count of blocks equals).
    $this->assertEquals(0, $processedAnnounces, 'No announces were processed.');
    // Try to emulate announce queue inclusion by fake count of blocks 2.
    $announceCount = $this->blockchainService->getApiService()->executeAnnounce([
      BlockchainRequestInterface::PARAM_COUNT => 2
    ]);
    $this->assertCount(1, $announceCount, 'Announce was related to one node.');
    $this->assertEquals(200, current($announceCount)->getStatusCode(), 'Status code for announce response is 200.');
    // Ensure 1 announce was processed as it was 200 (Due to fake count '2').
    $processedAnnounces = $this->blockchainService->getQueueService()->doAnnounceHandling();
    $this->assertEquals(1, $processedAnnounces, 'One announce was processed.');
    // In this case item was processed but taken no action as Fetch should have found that count of blocks equals.
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
