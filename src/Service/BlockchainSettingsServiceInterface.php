<?php

namespace Drupal\blockchain\Service;

/**
 * Interface BlockchainSettingsServiceInterface.
 *
 * @package Drupal\blockchain\Service
 */
interface BlockchainSettingsServiceInterface {

  /**
   * Getter for unique identifier as blockchain node.
   *
   * @return string
   *   Unique identifier.
   */
  function generateNodeName();

}