<?php

namespace Drupal\blockchain\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Blockchain Node entities.
 */
interface BlockchainNodeInterface extends ConfigEntityInterface {

  /**
   * Getter for entity type.
   *
   * @return string
   *   Id for entity type.
   */
  public static function entityTypeId();

  /**
   * Getter for ip address.
   *
   * @return string
   *   Value.
   */
  public function getIp();

  /**
   * Setter for ip address.
   *
   * @param string $ip
   *   Given ip address.
   *
   * @return $this
   *   Chaining.
   */
  public function setIp($ip);

}
