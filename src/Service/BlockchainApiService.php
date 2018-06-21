<?php

namespace Drupal\blockchain\Service;

use Drupal\blockchain\Entity\BlockchainBlockInterface;
use Drupal\blockchain\Utils\BlockchainRequestInterface;
use Drupal\blockchain\Utils\BlockchainResponse;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use function GuzzleHttp\Promise\settle;
use GuzzleHttp\Psr7\Response;
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

    $params = [];
    $this->addRequiredParams($params);

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
    } catch (\Exception $e) {
      $this->getLogger()
        ->error($e->getCode() . $e->getMessage() . $e->getTraceAsString());
      return BlockchainResponse::create();
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
    // Try to parse json.
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
  public function executeAnnounce(array $params) {

    $this->addRequiredParams($params);
    $endPoints = [];
    foreach ($this->blockchainNodeService->getList(0, $this->blockchainNodeService->getCount()) as $node) {
      $endPoints[$node->getEndPoint().static::API_ANNOUNCE] = $this->httpClient->postAsync($node->getEndPoint().static::API_ANNOUNCE, ['json' => $params]);
    }
    $results = settle($endPoints)->wait();
    $responses = [];
    foreach ($results as $url => $result) {
      if (isset($result['value']) && $result['value'] instanceof Response) {
        $responses[]= $result['value'];
      }
      else if (isset($result['reason']) && method_exists($result['reason'],'getResponse')) {
        $responses[]= $result['reason']->getResponse();
      }
    }

    return $responses;
  }

  /**
   * {@inheritdoc}
   */
  public function executeCount($url) {

    $params = [];
    $this->addRequiredParams($params);

    return $this->execute($url.static::API_COUNT, $params);
  }

  /**
   * {@inheritdoc}
   */
  public function executeFetch($url, BlockchainBlockInterface $blockchainBlock = NULL) {

    $params = [];
    $this->addRequiredParams($params);
    if ($blockchainBlock) {
      $params[BlockchainRequestInterface::PARAM_PREVIOUS_HASH] = $blockchainBlock->getPreviousHash();
      $params[BlockchainRequestInterface::PARAM_TIMESTAMP] = $blockchainBlock->getTimestamp();
    }

    return $this->execute($url.static::API_FETCH, $params);
  }

  /**
   * {@inheritdoc}
   */
  public function executePull($url, BlockchainBlockInterface $blockchainBlock, $count) {

    $params = [];
    $this->addRequiredParams($params);
    $params[BlockchainRequestInterface::PARAM_PREVIOUS_HASH] = $blockchainBlock->getPreviousHash();
    $params[BlockchainRequestInterface::PARAM_TIMESTAMP] = $blockchainBlock->getTimestamp();
    $params[BlockchainRequestInterface::PARAM_COUNT] = $count;

    return $this->execute($url.static::API_PULL, $params);
  }

  /**
   * {@inheritdoc}
   */
  public function addRequiredParams(array &$params) {

    $params[BlockchainRequestInterface::PARAM_SELF] = $this->configService->getCurrentConfig()->getNodeId();
    $params[BlockchainRequestInterface::PARAM_TYPE] = $this->configService->getCurrentConfig()->id();
    if ($this->configService->isBlockchainAuth()) {
      $params[BlockchainRequestInterface::PARAM_AUTH] = $this->configService->tokenGenerate();
    }
  }
}
