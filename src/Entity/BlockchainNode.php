<?php

namespace Drupal\blockchain\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Blockchain Node entity.
 *
 * @ConfigEntityType(
 *   id = "blockchain_node",
 *   label = @Translation("Blockchain Node"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\blockchain\BlockchainNodeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\blockchain\Form\BlockchainNodeForm",
 *       "edit" = "Drupal\blockchain\Form\BlockchainNodeForm",
 *       "delete" = "Drupal\blockchain\Form\BlockchainNodeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\blockchain\BlockchainNodeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "blockchain_node",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/blockchain_node/{blockchain_node}",
 *     "add-form" = "/admin/structure/blockchain_node/add",
 *     "edit-form" = "/admin/structure/blockchain_node/{blockchain_node}/edit",
 *     "delete-form" = "/admin/structure/blockchain_node/{blockchain_node}/delete",
 *     "collection" = "/admin/structure/blockchain_node"
 *   }
 * )
 */
class BlockchainNode extends ConfigEntityBase implements BlockchainNodeInterface {

  /**
   * The Blockchain Node ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Blockchain Node label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Blockchain Node ip/host address.
   *
   * @var string
   */
  protected $address;

  /**
   * The Blockchain Node port.
   *
   * @var string
   */
  protected $port;

  /**
   * Defines if protocol is secure.
   *
   * @var bool
   */
  protected $secure;

  /**
   * {@inheritdoc}
   */
  public static function entityTypeId() {
    return 'blockchain_node';
  }

  /**
   * {@inheritdoc}
   */
  public function setId($id) {
    $this->id = $id;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getId() {
    $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function getAddress() {
    return $this->address;
  }

  /**
   * {@inheritdoc}
   */
  public function setAddress($address) {
    $this->address = $address;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->label;
  }

  /**
   * {@inheritdoc}
   */
  public function setLabel($label) {
    $this->label = $label;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPort() {
    return $this->port;
  }

  /**
   * {@inheritdoc}
   */
  public function setPort($port) {
    $this->port = $port;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isSecure() {

    return $this->secure;
  }

  /**
   * {@inheritdoc}
   */
  public function setSecure($secure) {

    $this->secure = $secure;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getEndPoint() {

    $protocol = $this->isSecure()? 'https://' : 'http://';
    $protocol = is_null($this->isSecure())? '' : $protocol;
    $port = $this->getPort()? ':'. $this->getPort() : '';

    return $protocol . $this->getAddress() . $port;
  }

}
