<?php

namespace Drupal\blockchain\Service;
use Drupal\blockchain\Entity\BlockchainBlockInterface;
use Drupal\blockchain\Utils\BlockchainResponseInterface;
use GuzzleHttp\Psr7\Response;

/**
 * Interface BlockchainApiServiceInterface.
 *
 * @package Drupal\blockchain\Service
 */
interface BlockchainApiServiceInterface {

  const LOGGER_CHANNEL = 'blockchain.api';

  const API_SUBSCRIBE = '/blockchain/api/subscribe';

  const API_ANNOUNCE = '/blockchain/api/announce';

  const API_COUNT = '/blockchain/api/count';

  const API_FETCH = '/blockchain/api/fetch';

  const API_PULL = '/blockchain/api/pull';

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
   *
   * @return BlockchainResponseInterface|null
   *   Parsed json params as response or null.
   */
  public function execute($url, array $params);

  /**
   * Announces changes.
   *
   * @param array $params
   *   Required params.
   *
   * @return Response[]
   *   Array of responses if any.
   */
  public function executeAnnounce(array $params);

  /**
   * Executes post request by given url with given params in json format.
   *
   * @param string $url
   *   Full Url.
   *
   * @return BlockchainResponseInterface|null
   *   Parsed json params as response or null.
   */
  public function executeCount($url);

  /**
   * Executes post request by given url with given params in json format.
   *
   * @param string $url
   *   Full Url.
   * @param BlockchainBlockInterface $blockchainBlock
   *   Blockchain block.
   *
   * @return BlockchainResponseInterface|null
   *   Parsed json params as response or null.
   */
  public function executeFetch($url, BlockchainBlockInterface $blockchainBlock);

  /**
   * Executes post request by given url with given params in json format.
   *
   * @param string $url
   *   Full Url.
   * @param BlockchainBlockInterface $blockchainBlock
   *   Blockchain block.
   * @param $count
   *   String count of blocks to be fetched.
   *
   * @return BlockchainResponseInterface|null
   *   Parsed json params as response or null.
   */
  public function executePull($url, BlockchainBlockInterface $blockchainBlock, $count);

  /**
   * Adds common required params to request params array.
   *
   * @param array $params
   *   Given params.
   */
  public function addRequiredParams(array &$params);

}