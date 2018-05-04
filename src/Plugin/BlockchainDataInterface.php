<?php

namespace Drupal\blockchain\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Class BlockchainDataInterface.
 *
 * @package Drupal\blockchain\Plugin
 */
interface BlockchainDataInterface extends PluginInspectionInterface {

  /**
   * Setter for data.
   *
   * @param mixed $data
   *   SOme dat to be set.
   *
   * @return string|false
   *   Serialized data or false.
   */
  public function setData($data);

  /**
   * Getter for data.
   *
   * @return mixed
   *   Some object.
   */
  public function getData();

}