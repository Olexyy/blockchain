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
  const POW_POSITION_START = 'start';
  const POW_POSITION_END = 'end';
  const POW_EXPRESSION = '00';
  const DATA_HANDLER = 'simple';
  const FILTER_TYPE_BLACKLIST = 'blacklist';
  const FILTER_TYPE_WHITELIST = 'whitelist';
  const KEYS = [
    'blockchainType', 'blockchainId', 'blockchainNodeId', 'poolManagement',
    'announceManagement', 'intervalPool', 'intervalAnnounce', 'powPosition',
    'powExpression', 'dataHandler', 'blockchainAuth', 'blockchainFilterType',

  ];

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

  /**
   * Getter for pool management.
   *
   * @return string
   *   Pool management.
   */
  public function getPoolManagement();

  /**
   * Setter for pool management.
   *
   * @param string $poolManagement
   *   Type of poll management.
   *
   * @return $this
   *   Chaining.
   */
  public function setPoolManagement($poolManagement);

  /**
   * Getter for announce management.
   *
   * @return string
   *   Announce management.
   */
  public function getAnnounceManagement();

  /**
   * Setter for announce management.
   *
   * @param string $announceManagement
   *   Type of announce management.
   *
   * @return $this
   *   Chaining.
   */
  public function setAnnounceManagement($announceManagement);

  /**
   * Getter for POW position.
   *
   * @return string
   *   POW position.
   */
  public function getPowPosition();

  /**
   * Setter for POW position.
   *
   * @param string $powPosition
   *   POW position.
   *
   * @return $this
   *   Chaining.
   */
  public function setPowPosition($powPosition);

  /**
   * Getter for POW expression.
   *
   * @return string
   *   Announce management.
   */
  public function getPowExpression();

  /**
   * Setter for POW expression.
   *
   * @param string $powExpression
   *   POW expression.
   *
   * @return $this
   *   Chaining.
   */
  public function setPowExpression($powExpression);

  /**
   * Getter for interval of pool management.
   *
   * @return string
   *   Interval.
   */
  public function getIntervalPool();

  /**
   * Setter for interval of pool management.
   *
   * @param string $intervalPool
   *   Interval.
   *
   * @return $this
   *   Chaining.
   */
  public function setIntervalPool($intervalPool);

  /**
   * Getter for interval of announce management.
   *
   * @return string
   *   Interval.
   */
  public function getIntervalAnnounce();

  /**
   * Setter for interval of announce management.
   *
   * @param string $intervalAnnounce
   *   Interval.
   *
   * @return $this
   *   Chaining.
   */
  public function setIntervalAnnounce($intervalAnnounce);

  /**
   * Getter for auth setting.
   *
   * @return bool
   *   Result.
   */
  public function isBlockchainAuth();

  /**
   * Setter for auth setting.
   *
   * @param $blockchainAuth
   *    Given value.
   *
   * @return $this
   *   Chaining.
   */
  public function setBlockchainAuth($blockchainAuth);

  /**
   * Getter for blockchain nodes filter type.
   *
   * @return string
   *   Value.
   */
  public function getBlockchainFilterType();

  /**
   * Setter for blockchain nodes filter type.
   *
   * @param $blockchainFilterType
   *    Given value.
   *
   * @return $this
   *   Chaining.
   */
  public function setBlockchainFilterType($blockchainFilterType);

}