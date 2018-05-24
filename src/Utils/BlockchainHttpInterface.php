<?php

namespace Drupal\blockchain\Utils;


interface BlockchainHttpInterface {

  /**
   * Getter for param if exists.
   *
   * @param string $key
   *   Name of param.
   * @return string|array|null
   *   Value if any.
   */
  public function getParam($key);

  /**
   * Setter for property.
   *
   * @param string $key
   *   Name of param.
   * @param string $value
   *   Value of param.
   *
   * @return $this
   *   Chaining.
   */
  public function setParam($key, $value);

  /**
   * Getter for array of params.
   *
   * @return array
   *   Params.
   */
  public function getParams();

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
   * Getter for port.
   *
   * @return string
   *   Value.
   */
  public function getPort();

  /**
   * Setter for port.
   *
   * @param string $port
   *   Given port.
   *
   * @return $this
   *   Chaining.
   */
  public function setPort($port);

  /**
   * Defines if protocol.
   *
   * @return bool
   *   Test result.
   */
  public function isSecure();

  /**
   * Setter for protocol security.
   *
   * @param bool $secure
   *   Value.
   *
   * @return $this
   *   Chaining.
   */
  public function setSecure($secure);

  /**
   * Endpoint ready to be requested.
   *
   * @return string
   *   Endpoint.
   */
  public function getEndPoint();

}