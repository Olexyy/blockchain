<?php

namespace Drupal\blockchain\Service;
use Drupal\blockchain\Utils\BlockchainResponseInterface;

/**
 * Interface BlockchainApiServiceInterface.
 *
 * @package Drupal\blockchain\Service
 */
interface BlockchainApiServiceInterface {

  const LOGGER_CHANNEL = 'blockchain.api';

  const API_SUBSCRIBE = '/blockchain/api/subscribe';

  const API_ANNOUNCE = '/blockchain/api/announce';

  /**
   * Getter for current request.
   *
   * @return null|\Symfony\Component\HttpFoundation\Request
   */
  public function getCurrentRequest();

  /**
   * Getter for logger.
   *
   * @return \Drupal\Core\Logger\LoggerChannelInterface
   */
  public function getLogger();

  /**
   * Executes subscribe action on given base url.
   *
   * @param string $baseUrl
   *   Base url of request.
   *
   * @return BlockchainResponseInterface|null
   *   Parsed json params as response or null.
   */
  public function executeSubscribe($baseUrl);

  /**
   * Executes post request by given url with given params in json format.
   *
   * @param string $url
   *   Full Url.
   * @param array $params
   *   Params to be passed.
   * @return BlockchainResponseInterface|null
   *   Parsed json params as response or null.
   */
  public function execute($url, array $params);

  /**
   * Announces changes.
   *
   * @param array $params
   *   Required params.
   */
  public function announceAll(array $params);

}