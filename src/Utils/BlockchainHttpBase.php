<?php

namespace Drupal\blockchain\Utils;

/**
 * Class BlockchainHttpBase.
 *
 * @package Drupal\blockchain\Utils
 */
abstract class BlockchainHttpBase implements BlockchainHttpInterface {

  /**
   * Parameters.
   *
   * @var array
   */
  protected $params;

  /**
   * Ip address.
   *
   * @var string
   */
  protected $ip;

  /**
   * Port.
   *
   * @var string
   */
  protected $port;

  /**
   * Protocol security.
   *
   * @var bool
   */
  protected $secure;

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

  /**
   * {@inheritdoc}
   */
  public function getPort() {
    return $this->port;
  }

  /**
   * {@inheritdoc}
   */
  public function setPort($port) {
    $this->port = $port;
    return $this;
  }

  /**
   * Defines if protocol.
   *
   * @return bool
   *   Test result.
   */
  public function isSecure() {

    return $this->secure;
  }

  /**
   * Setter for protocol security.
   *
   * @param bool $secure
   *   Value.
   *
   * @return $this
   *   Chaining.
   */
  public function setSecure($secure) {

    $this->secure = $secure;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getEndPoint() {

    $protocol = $this->isSecure()? 'https://' : 'http://';
    $port = $this->getPort()? ':'. $this->getPort() : '';
    return $protocol . $this->getIp() . $port;
  }

}