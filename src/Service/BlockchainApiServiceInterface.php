<?php

namespace Drupal\blockchain\Service;

/**
 * Interface BlockchainApiServiceInterface.
 *
 * @package Drupal\blockchain\Service
 */
interface BlockchainApiServiceInterface {

  const LOGGER_CHANNEL = 'blockchain.api';

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
   * @return array|null
   *   Parsed json params or null.
   */
  public function executeSubscribe($baseUrl);

  /**
   * Executes post request by given url with given params in json format.
   *
   * @param string $url
   *   Full Url.
   * @param array $params
   *   Params to be passed.
   * @return null|array
   *   Parsed json params or null.
   */
  public function execute($url, array $params);
}