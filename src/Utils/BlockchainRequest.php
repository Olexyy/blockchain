<?php

namespace Drupal\blockchain\Utils;

use Drupal\Component\Utility\Xss;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BlockchainRequest.
 *
 * @package Drupal\blockchain\Utils
 */
class BlockchainRequest extends BlockchainHttpBase implements BlockchainRequestInterface {

  /**
   * Type.
   *
   * @var string
   */
  protected $type;

  /**
   * Valid state.
   *
   * @var bool
   */
  protected $valid;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $params, $type, $ip) {

    $this->type = $type;
    $this->params = $params;
    $this->ip = $ip;
  }

  /**
   * {@inheritdoc}
   */
  public function setValid($valid) {

    $this->valid = $valid;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isValid() {

    return $this->valid;
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {

    return $this->type;
  }

  /**
   * {@inheritdoc}
   */
  public function create(array $params, $type, $ip) {

    return new static($params, $type, $ip);
  }

  /**
   * {@inheritdoc}
   */
  public static function createFromRequest(Request $request, $type) {

    return new static(static::parseRequest($request), $type, $request->getClientIp());
  }

  /**
   * Extracts params array fom request.
   *
   * @param Request $request
   *   Request object.
   *
   * @return array
   *   Parsed params.
   */
  protected static function parseRequest(Request $request) {

    $params = [];
    if ($data = $request->getContent()) {
      if ($jsonData = json_decode($data, TRUE)) {

        if (is_array($jsonData)) {
          foreach (static::PARAMS as $param) {
            if (isset($jsonData[$param]) && $value = $jsonData[$param]) {
              $params[$param] = $jsonData[$param];
            }
          }
        }
        static::arrayXssRecursive($params);
      }
    }

    return $params;
  }

  /**
   * Filters params by xss filter.
   *
   * @param array $params
   *   Given params.
   */
  protected static function arrayXssRecursive(array &$params) {

    foreach ($params as &$param) {
      if (is_array($param)) {
        static::arrayXssRecursive($param);
      }
      else {
        $param = Xss::filter($param);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getAuthParam() {
    return $this->getParam(static::PARAM_AUTH);
  }

  /**
   * {@inheritdoc}
   */
  public function getSelfParam() {
    return $this->getParam(static::PARAM_SELF);
  }

  /**
   * {@inheritdoc}
   */
  public function getCountParam() {
    return $this->getParam(static::PARAM_COUNT);
  }

  /**
   * {@inheritdoc}
   */
  public function getBlocksParam() {
    return $this->getParam(static::PARAM_BLOCKS);
  }

  /**
   * {@inheritdoc}
   */
  public function getPreviousHashParam() {
    return $this->getParam(static::PARAM_PREVIOUS_HASH);
  }

  /**
   * {@inheritdoc}
   */
  public function getTimestampParam() {
    return $this->getParam(static::PARAM_TIMESTAMP);
  }

  /**
   * {@inheritdoc}
   */
  public function hasAuthParam() {
    return !($this->getAuthParam() === NULL);
  }

  /**
   * {@inheritdoc}
   */
  public function hasSelfParam() {
    return !($this->getSelfParam() === NULL);
  }

  /**
   * {@inheritdoc}
   */
  public function hasCountParam() {
    return !($this->getCountParam() === NULL);
  }

  /**
   * {@inheritdoc}
   */
  public function hasBlocksParam() {
    return !($this->getBlocksParam() === NULL);
  }

  /**
   * {@inheritdoc}
   */
  public function hasTimestampParam() {
    return !($this->getTimestampParam() === NULL);
  }

  /**
   * {@inheritdoc}
   */
  public function hasPreviousHashParam() {
    return !($this->getPreviousHashParam() === NULL);
  }

  /**
   * Serializer.
   *
   * @return string
   */
  public function sleep() {

    return serialize($this);
  }

  /**
   * Deserializer.
   *
   * @param string $data
   *   Data to be deserialize.
   *
   * @return $this
   *   This object.
   */
  public static function wakeup($data) {

    return unserialize($data);
  }

}
