<?php

namespace Drupal\Tests\blockchain\Functional;

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
    // Blockchain node id is generated on first request, lets check it.
    $blockchainNodeId = $this->blockchainService->getConfigService()->getBlockchainNodeId();
    $this->assertNotEmpty($blockchainNodeId, 'Blockchain node id is generated.');
    $this->assertEquals($blockchainNodeId, $this->blockchainService->getConfigService()->getBlockchainNodeId(),
      'Blockchain id si not regenerated on second call');
    // Ensure Blockchain type is 'single'.
    $type = $this->blockchainService->getConfigService()->getBlockchainType();
    $this->assertEquals($type, BlockchainConfigServiceInterface::TYPE_SINGLE, 'Blockchain type is single');
    // Cover API is restricted for 'single' type.
    $response = $this->blockchainService->getApiService()->executeSubscribe($this->baseUrl);
    $this->assertEquals(403, $response->getStatusCode());
    $this->assertEquals('Forbidden', $response->getMessageParam());
    $this->assertEquals('Access to this resource is restricted.', $response->getDetailsParam());
    // Set and ensure blockchain type is 'multiple'.
    $this->blockchainService->getConfigService()->setBlockchainType(BlockchainConfigServiceInterface::TYPE_MULTIPLE);
    $type = $this->blockchainService->getConfigService()->getBlockchainType();
    $this->assertEquals($type, BlockchainConfigServiceInterface::TYPE_MULTIPLE, 'Blockchain type is multiple');
    // Try to access with no 'self' param.
    $response = $this->blockchainService->getApiService()->execute($this->blockchainSubscribeUrl, [
    ]);
    $this->assertEquals(400, $response->getStatusCode());
    $this->assertEquals('Bad request', $response->getMessageParam());
    $this->assertEquals('No self param.', $response->getDetailsParam());
    //

  }

}
