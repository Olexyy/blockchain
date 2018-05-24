<?php

namespace Drupal\blockchain\Service;

use Drupal\blockchain\Utils\BlockchainRequestInterface;
use Drupal\blockchain\Utils\BlockchainResponse;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\Promise;
use function GuzzleHttp\Promise\settle;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class BlockchainAPIService.
 *
 * @package Drupal\blockchain\Service
 */
class BlockchainApiService implements BlockchainApiServiceInterface {

  /**
   * Request stack.
   *
   * @var RequestStack
   */
  protected $requestStack;

  /**
   * Http client.
   *
   * @var Client
   */
  protected $httpClient;

  /**
   * Logger interface.
   *
   * @var LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * Blockchain config service.
   *
   * @var BlockchainConfigServiceInterface
   */
  protected $configService;

  /**
   * Blockchain node service.
   *
   * @var BlockchainNodeServiceInterface
   */
  protected $blockchainNodeService;

  /**
   * {@inheritdoc}
   */
  public function __construct(RequestStack $requestStack,
                              Client $httpClient,
                              LoggerChannelFactoryInterface $loggerChannelFactory,
                              BlockchainConfigServiceInterface $blockchainSettingsService,
                              BlockchainNodeServiceInterface $blockchainNodeService) {

    $this->requestStack = $requestStack;
    $this->httpClient = $httpClient;
    $this->loggerFactory = $loggerChannelFactory;
    $this->configService = $blockchainSettingsService;
    $this->blockchainNodeService = $blockchainNodeService;
  }

  /**
   * {@inheritdoc}
   */
  public function getLogger() {

    return $this->loggerFactory->get(static::LOGGER_CHANNEL);
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrentRequest() {

    return $this->requestStack->getCurrentRequest();
  }

  /**
   * {@inheritdoc}
   */
  public function executeSubscribe($baseUrl) {

    $params = [
      BlockchainRequestInterface::PARAM_SELF => $this->configService->getBlockchainNodeId(),
    ];
    if ($this->configService->isBlockchainAuth()) {
      $params[BlockchainRequestInterface::PARAM_AUTH] = $this->configService->tokenGenerate();
    }

    return $this->execute($baseUrl.static::API_SUBSCRIBE, $params);
  }

  /**
   * {@inheritdoc}
   */
  public function execute($url, array $params) {

    try {
      $response = $this->httpClient->request('POST', $url, ['json' => $params]);
      $body = json_decode($response->getBody()->getContents(), TRUE);

      return BlockchainResponse::create()
        ->setStatusCode($response->getStatusCode())
        ->setParams($body);
    } catch (GuzzleException $e) {
      $this->getLogger()
        ->error($e->getCode() . $e->getMessage() . $e->getTraceAsString());

      return BlockchainResponse::create()
        ->setStatusCode($e->getCode())
        ->setParams($this->exceptionMessageToArray($e));
    }
  }

  /**
   * Internal helper to parse Exception.
   *
   * @param GuzzleException $exception
   *   Given exception.
   *
   * @return array
   *   Array of parsed values.
   */
  protected function exceptionMessageToArray(GuzzleException $exception) {

    $message = $exception->getMessage();
    $parsed = explode('response:', $message);
    // Try to pars json.
    if (count($parsed) === 2 && ($jsonData = (array)json_decode(trim($parsed[1])))) {
      return $jsonData;
    }
    else {
      return [
        'message' => $message,
        'details' => $exception->getTraceAsString(),
      ];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function announceAll(array $params) {

    $params[BlockchainRequestInterface::PARAM_SELF] = $this->configService->getBlockchainNodeId();
    if ($this->configService->isBlockchainAuth()) {
      $params[BlockchainRequestInterface::PARAM_AUTH] = $this->configService->tokenGenerate();
    }
    $endPoints = [];
    foreach ($this->blockchainNodeService->getList(0, $this->blockchainNodeService->getCount()) as $node) {
      $endPoints[$node->getEndPoint()] = $this->httpClient->postAsync($node->getEndPoint(), ['json' => $params]);
    }
    // Don't care about results...
    // Wait for the requests to complete, even if some of them fail
    // LATER SOME LOGGING
    //$results = settle($endPoints)->wait();
  }
}
