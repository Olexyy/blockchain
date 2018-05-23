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

  /**
   * Getter for id.
   *
   * @return string
   *   Value.
   */
  public function getId();

  /**
   * Setter for id.
   *
   * @param string $id
   *   Given ip address.
   *
   * @return $this
   *   Chaining.
   */
  public function setId($id);

  /**
   * Getter for label.
   *
   * @return string
   *   Value.
   */
  public function getLabel();

  /**
   * Setter for label.
   *
   * @param string $label
   *   Given label.
   *
   * @return $this
   *   Chaining.
   */
  public function setLabel($label);

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

}
