<?php

namespace Drupal\blockchain\Utils;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface BlockchainRequestInterface.
 *
 * @package Drupal\blockchain\Utils
 */
interface BlockchainRequestInterface extends BlockchainHttpInterface {

  const TYPE_SUBSCRIBE = 'subscribe';
  const TYPE_ANNOUNCE = 'announce';
  const TYPE_COUNT = 'count';
  const TYPE_FETCH = 'fetch';
  const TYPE_PULL = 'pull';
  const PARAM_AUTH = 'auth'; // hash of (self+bc_token)
  const PARAM_SELF = 'self';
  const PARAM_TIMESTAMP = 'timestamp';
  const PARAM_PREVIOUS_HASH = 'previous_hash';
  const PARAMS = [
    self::PARAM_AUTH, self::PARAM_SELF, self::PARAM_COUNT, self::PARAM_BLOCKS,
    self::PARAM_TIMESTAMP, self::PARAM_PREVIOUS_HASH,
  ];

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
  public function hasBlocksParam();

  /**
   * Predicate.
   *
   * @return bool
   *   Test result.
   */
  public function hasTimestampParam();

  /**
   * Getter for timestamp property.
   *
   * @return string
   *   Value.
   */
  public function getTimestampParam();

  /**
   * Predicate.
   *
   * @return bool
   *   Test result.
   */
  public function hasPreviousHashParam();

  /**
   * Getter for previous hash property.
   *
   * @return string
   *   Value.
   */
  public function getPreviousHashParam();

  /**
   * Serializer.
   *
   * @return string
   */
  public function sleep();

  /**
   * Deserializer.
   *
   * @param string $data
   *   String data.
   *
   * @return $this
   *   This object.
   */
  public static function wakeup($data);

}
