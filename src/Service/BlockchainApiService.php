<?php

namespace Drupal\blockchain\Service;

use GuzzleHttp\Client;
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
   * {@inheritdoc}
   */
  public function __construct(RequestStack $requestStack, Client $httpClient) {

    $this->requestStack = $requestStack;
    $this->httpClient = $httpClient;
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrentRequest() {
    return $this->requestStack->getCurrentRequest();
  }

}
