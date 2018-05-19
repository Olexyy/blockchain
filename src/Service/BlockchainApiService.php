<?php

namespace Drupal\blockchain\Service;

use Drupal\blockchain\Utils\BlockchainRequestInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
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
   * {@inheritdoc}
   */
  public function __construct(RequestStack $requestStack,
                              Client $httpClient,
                              LoggerChannelFactoryInterface $loggerChannelFactory,
                              BlockchainConfigServiceInterface $blockchainSettingsService) {

    $this->requestStack = $requestStack;
    $this->httpClient = $httpClient;
    $this->loggerFactory = $loggerChannelFactory;
    $this->configService = $blockchainSettingsService;
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

    return $this->execute($baseUrl.'/blockchain/subscribe', $params);
  }

  /**
   * {@inheritdoc}
   */
  public function execute($url, array $params) {

    try {
      $response = $this->httpClient->request('POST', $url, ['json' => $params]);
      if ($response->getStatusCode() === 200) {
        $body = $response->getBody()->getContents();

        return json_decode($body);
      }
      else {
        $this->getLogger()->error('Request to @url result: code - @code, details: @details', [
          '@url' => $url,
          '@code' => $response->getStatusCode(),
          '@details' => $response->getReasonPhrase(),
        ]);

        return NULL;
      }
    } catch (GuzzleException $e) {
      $this->getLogger()->error($e->getMessage() . $e->getTraceAsString());

      return NULL;
    }
  }

}
