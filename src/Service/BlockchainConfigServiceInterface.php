<?php

namespace Drupal\blockchain\Service;

use Drupal\Core\Config\Config;

/**
 * Interface BlockchainConfigServiceInterface.
 *
 * @package Drupal\blockchain\Service
 */
interface BlockchainConfigServiceInterface {

  /**
   * Getter for unique identifier as blockchain node.
   *
   * @return string
   *   Unique identifier.
   */
  function generateNodeName();

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

}