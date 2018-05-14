<?php

namespace Drupal\blockchain\Service;

/**
 * Interface BlockchainApiServiceInterface.
 *
 * @package Drupal\blockchain\Service
 */
interface BlockchainApiServiceInterface {

  /**
   * Getter for current request.
   *
   * @return null|\Symfony\Component\HttpFoundation\Request
   */
  public function getCurrentRequest();
}