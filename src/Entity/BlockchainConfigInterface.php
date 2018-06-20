<?php

namespace Drupal\blockchain\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Blockchain config entities.
 */
interface BlockchainConfigInterface extends ConfigEntityInterface {

  const ENTITY_TYPE = 'blockchain_config';
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

  // Add get/set methods for your configuration properties here.
}
