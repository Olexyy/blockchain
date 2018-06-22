<?php

namespace Drupal\blockchain\Plugin;

use Drupal\blockchain\Utils\BlockchainRequestInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Class BlockchainAuthInterface.
 *
 * @package Drupal\blockchain\Plugin
 */
interface BlockchainAuthInterface extends PluginInspectionInterface {

  const LOGGER_CHANNEL = 'blockchain_auth';

  /**
   * Auth handler.
   *
   * @param BlockchainRequestInterface $blockchainRequest
   *   Given request.
   *
   * @return bool
   *   Auth result.
   */
  public function authorize(BlockchainRequestInterface $blockchainRequest);

  /**
   * Setup for auth params.
   *
   * @param array $params
   *   Given params.
   */
  public function addAuthParams(array &$params);

}
