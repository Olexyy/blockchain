<?php

namespace Drupal\blockchain\Service;

use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\State\StateInterface;


/**
 * Class BlockchainConfigServiceInterface.
 *
 * @package Drupal\blockchain\Service
 */
class BlockchainConfigService implements BlockchainConfigServiceInterface {

  /**
   * Uuid service.
   *
   * @var UuidInterface
   */
  protected $uuid;

  /**
   * State service.
   *
   * @var StateInterface
   */
  protected $state;

  /**
   * ConfigFactory service.
   *
   * @var ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * BlockchainConfigServiceInterface constructor.
   *
   * {@inheritdoc}
   */
  public function __construct(UuidInterface $uuid,
                              StateInterface $state,
                              ConfigFactoryInterface $configFactory) {

    $this->uuid = $uuid;
    $this->state = $state;
    $this->configFactory = $configFactory;
  }

  /**
   * {@inheritdoc}
   */
  public function generateId() {

    return $this->uuid->generate();
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig($editable = FALSE) {

    if ($editable) {
      return $this->configFactory->getEditable('blockchain.config');
    }

    return $this->configFactory->get('blockchain.config');
  }

  /**
   * {@inheritdoc}
   */
  public function getState() {

    return $this->state->get('blockchain.state', []);
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockchainId() {

    if (!$blockchain_id = $this->getConfig()->get('blockchain_id')) {
      $blockchain_id = $this->generateId();
      $this->setBlockchainId($blockchain_id);
    }

    return $blockchain_id;
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockchainNodeId() {

    if (!$blockchain_node_id = $this->getConfig()->get('blockchain_node_id')) {
      $blockchain_node_id = $this->generateId();
      $this->setBlockchainNodeId($blockchain_node_id);
    }

    return $blockchain_node_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setBlockchainId($blockchain_id = NULL) {
    $blockchain_id = $blockchain_id? $blockchain_id : $this->generateId();
    $this->getConfig(TRUE)->set('blockchain_id', $blockchain_id)->save();
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setBlockchainNodeId($blockchain_node_id = NULL) {
    $blockchain_node_id = $blockchain_node_id? $blockchain_node_id : $this->generateId();
    $this->getConfig(TRUE)->set('blockchain_node_id', $blockchain_node_id)->save();
    return $this;
  }
}