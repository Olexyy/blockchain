<?php

namespace Drupal\blockchain\Utils;

use Drupal\blockchain\Service\BlockchainServiceInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface BlockchainRequestInterface.
 *
 * @package Drupal\blockchain\Utils
 */
interface BlockchainRequestInterface {

  const TYPE_SUBSCRIBE = 'subscribe';
  const TYPE_ANNOUNCE = 'announce';
  const TYPE_GET_COUNT = 'count';
  const TYPE_GET_BLOCKS = 'blocks';
  const PARAM_AUTH = 'auth'; // hash of (self+bc_token)
  const PARAM_SELF = 'self';
  const PARAM_COUNT = 'count';
  const PARAM_BLOCKS = 'blocks';
  const PARAMS = [
    self::PARAM_AUTH, self::PARAM_SELF, self::PARAM_COUNT, self::PARAM_BLOCKS,
  ];

  /**
   * Getter for param if exists.
   *
   * @return string|array|null
   *   Value if any.
   */
  public function getParam($key);

  /**
   * Getter for array of params.
   *
   * @return array
   *   Params.
   */
  public function getParams();

  /**
   * Getter for param.
   *
   * @return string
   *   Value.
   */
  public function getAuthParam();

  /**
   * Getter for param.
   *
   * @return string
   *   Value.
   */
  public function getSelfParam();

  /**
   * Getter for param.
   *
   * @return string
   *   Value.
   */
  public function getCountParam();

  /**
   * Getter for param.
   *
   * @return array
   *   Value.
   */
  public function getBlocksParam();

  /**
   * Getter for type property.
   *
   * @return string
   *   Value.
   */
  public function getType();

  /**
   * BlockchainRequestInterface constructor.
   *
   * @param array $params
   *   Params.
   * @param string $type
   *   Type.
   * @param string $ip
   *   Client ip.
   */
  public function __construct(array $params, $type, $ip);

  /**
   * Setter for is valid property.
   *
   * @param bool $valid
   *   Value.
   *
   * @return static
   *   This object.
   */
  public function setValid($valid);

  /**
   * Predicate to define validness.
   *
   * @return bool
   *   Test result.
   */
  public function isValid();

  /**
   * Factory method.
   *
   * @param array $params
   *   Params.
   * @param string $type
   *   Type.
   * @param string $ip
   *   Client ip.
   *
   * @return static
   *   This object.
   */
  public function create(array $params, $type, $ip);

  /**
   * BlockchainRequestInterface constructor.
   *
   * @param Request $request
   *   Request.
   * @param string $type
   *   Type.
   *
   * @return static
   *   This object.
   */
  public static function createFromRequest(Request $request, $type);

  /**
   * Getter for ip.
   *
   * @return string
   *   Value.
   */
  public function getIp();

  /**
   * Setter for property.
   *
   * @param string $ip
   *   Value.
   *
   * @return $this
   *   Chaining.
   */
  public function setIp($ip);

  /**
   * Predicate.
   *
   * @param string $key
   *   Name of key.
   *
   * @return bool
   *   Test result.
   */
  public function hasParam($key);

  /**
   * Predicate.
   *
   * @return bool
   *   Test result.
   */
  public function hasAuthParam();

  /**
   * Predicate.
   *
   * @return bool
   *   Test result.
   */
  public function hasSelfParam();

  /**
   * Predicate.
   *
   * @return bool
   *   Test result.
   */
  public function hasCountParam();

  /**
   * Predicate.
   *
   * @return bool
   *   Test result.
   */
  public function hasBlocksParam();

}