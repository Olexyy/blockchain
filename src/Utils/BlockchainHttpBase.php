<?php

namespace Drupal\blockchain\Utils;

/**
 * Class BlockchainHttpBase.
 *
 * @package Drupal\blockchain\Utils
 */
abstract class BlockchainHttpBase implements BlockchainHttpInterface {

  protected $params;

  protected $ip;

  /**
   * {@inheritdoc}
   */
  public function getParam($key) {

    if (isset($this->params[$key])) {
      return $this->params[$key];
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setParam($key, $value) {

    $this->params[$key] = $value;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getParams() {
    return $this->params;
  }

  /**
   * {@inheritdoc}
   */
  public function hasParam($key) {
    return !($this->getParam($key) === NULL);
  }

  /**
   * {@inheritdoc}
   */
  public function getIp() {
    return $this->ip;
  }

  /**
   * {@inheritdoc}
   */
  public function setIp($ip) {

    $this->ip = $ip;
    return $this;
  }

}