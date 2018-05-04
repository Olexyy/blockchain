<?php

namespace Drupal\blockchain\Service;

use Drupal\Core\Config\Config;

/**
 * Interface BlockchainConfigServiceInterface.
 *
 * @package Drupal\blockchain\Service
 */
interface BlockchainConfigServiceInterface {

  const TYPE_SINGLE = 'single';
  const TYPE_MULTIPLE = 'multiple';
  const POOL_MANAGEMENT_MANUAL = 'manual';
  const POOL_MANAGEMENT_CRON = 'cron';
  const ANNOUNCE_MANAGEMENT_CRON = 'cron';
  const ANNOUNCE_MANAGEMENT_IMMEDIATE = 'immediate';
  const INTERVAL_DEFAULT = 60 * 10;
  const POF_POSITION_START = 'start';
  const POF_POSITION_END = 'end';
  const POF_EXPRESSION = '00';

  /**
   * Getter for unique identifier.
   *
   * @return string
   *   Unique identifier.
   */
  function generateId();

  /**
   * Getter for config.
   *
   * @param bool $editable
   *   Defines result.
   *
   * @return \Drupal\Core\Config\Config|\Drupal\Core\Config\ImmutableConfig|Config
   */
  function getConfig($editable = FALSE);

  /**
   * State values.
   *
   * @return array
   *   Values for given state.
   */
  public function getState();

  /**
   * Getter for blockchain identifier.
   *
   * @return string
   *   UUID.
   */
  public function getBlockchainId();

  /**
   * Setter for blockchain identifier.
   *
   * @param string|null $blockchain_id
   *   Given UUID.
   *
   * @return $this
   *   Chaining.
   */
  public function setBlockchainId($blockchain_id = NULL);

  /**
   * Getter for blockchain node identifier.
   *
   * @return string
   *   UUID.
   */
  public function getBlockchainNodeId();

  /**
   * Setter for blockchain node identifier.
   *
   * @param string|null $blockchain_node_id
   *   Given UUID.
   *
   * @return $this
   *   Chaining.
   */
  public function setBlockchainNodeId($blockchain_node_id = NULL);

  /**
   * Getter for blockchain type.
   *
   * @return string
   *   Blockchain type.
   */
  public function getBlockchainType();

  /**
   * Setter for blockchain type.
   *
   * @param string $blockchainType
   *   Type.
   *
   * @return $this
   *   Chaining.
   */
  public function setBlockchainType($blockchainType);

}