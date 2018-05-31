<?php

namespace Drupal\blockchain\Utils;


interface BlockchainHttpInterface {

  const PARAM_INTERVAL = 'interval';
  
  const PARAM_COUNT = 'count';

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

  /**
   * Predicate.
   *
   * @return bool
   *   Test result.
   */
  public function hasCountParam();

  /**
   * Getter for param.
   *
   * @return string
   *   Value.
   */
  public function getCountParam();

  /**
   * Setter for param.
   *
   * @param string $value
   *   Given value.
   *
   * @return $this
   *   Chaining.
   */
  public function setCountParam($value);


  /**
   * Getter for interval property.
   *
   * @return string
   *   Value.
   */
  public function getIntervalParam();

  /**
   * Setter for interval property.
   *
   * @param string $value
   *   Given value.
   *
   * @return $this
   *   Chaining.
   */
  public function setIntervalParam($value);

  /**
   * Predicate.
   *
   * @return bool
   *   Test result.
   */
  public function hasIntervalParam();

}