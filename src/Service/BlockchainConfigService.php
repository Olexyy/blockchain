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

    if (!$blockchain_id = $this->getConfig()->get('blockchainId')) {
      $blockchain_id = $this->generateId();
      $this->setBlockchainId($blockchain_id);
    }

    return $blockchain_id;
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockchainNodeId() {

    if (!$blockchain_node_id = $this->getConfig()->get('blockchainNodeId')) {
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
    $this->getConfig(TRUE)
      ->set('blockchainId', $blockchain_id)
      ->save();

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setBlockchainNodeId($blockchain_node_id = NULL) {

    $blockchain_node_id = $blockchain_node_id? $blockchain_node_id : $this->generateId();
    $this->getConfig(TRUE)
      ->set('blockchainNodeId', $blockchain_node_id)
      ->save();

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockchainType() {

    if(!($blockchainType = $this->getConfig()->get('blockchainType'))) {
      $blockchainType = static::TYPE_SINGLE;
      $this->setBlockchainType($blockchainType);
    }

    return $blockchainType;
  }

  /**
   * {@inheritdoc}
   */
  public function setBlockchainType($blockchainType) {

    $this->getConfig(TRUE)
      ->set('blockchainType', $blockchainType)
      ->save();

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPoolManagement() {

    if(!($poolManagement = $this->getConfig()->get('poolManagement'))) {
      $poolManagement = static::POOL_MANAGEMENT_MANUAL;
      $this->setPoolManagement($poolManagement);
    }

    return $poolManagement;
  }

  /**
   * {@inheritdoc}
   */
  public function setPoolManagement($poolManagement) {

    $this->getConfig(TRUE)
      ->set('poolManagement', $poolManagement)
      ->save();

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getAnnounceManagement() {

    if(!($announceManagement = $this->getConfig()->get('announceManagement'))) {
      $announceManagement = static::ANNOUNCE_MANAGEMENT_IMMEDIATE;
      $this->setAnnounceManagement($announceManagement);
    }

    return $announceManagement;
  }

  /**
   * {@inheritdoc}
   */
  public function setAnnounceManagement($announceManagement) {

    $this->getConfig(TRUE)
      ->set('announceManagement', $announceManagement)
      ->save();

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPowPosition() {

    if(!($powPosition = $this->getConfig()->get('powPosition'))) {
      $powPosition = static::POF_POSITION_START;
      $this->setPowPosition($powPosition);
    }

    return $powPosition;
  }

  /**
   * {@inheritdoc}
   */
  public function setPowPosition($powPosition) {

    $this->getConfig(TRUE)
      ->set('powPosition', $powPosition)
      ->save();

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPowExpression() {

    if(!($powExpression = $this->getConfig()->get('powExpression'))) {
      $powExpression = '00';
      $this->setPowExpression($powExpression);
    }

    return $powExpression;
  }

  /**
   * {@inheritdoc}
   */
  public function setPowExpression($powExpression) {

    $this->getConfig(TRUE)
      ->set('powExpression', $powExpression)
      ->save();

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getIntervalPool() {

    if(!($intervalPool = $this->getConfig()->get('intervalPool'))) {
      $intervalPool = static::INTERVAL_DEFAULT;
      $this->setIntervalPool($intervalPool);
    }

    return $intervalPool;
  }

  /**
   * {@inheritdoc}
   */
  public function setIntervalPool($intervalPool) {

    $this->getConfig(TRUE)
      ->set('intervalPool', $intervalPool)
      ->save();

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getIntervalAnnounce() {

    if(!($intervalAnnounce = $this->getConfig()->get('intervalAnnounce'))) {
      $intervalAnnounce = static::INTERVAL_DEFAULT;
      $this->setIntervalPool($intervalAnnounce);
    }

    return $intervalAnnounce;
  }

  /**
   * {@inheritdoc}
   */
  public function setIntervalAnnounce($intervalAnnounce) {

    $this->getConfig(TRUE)
      ->set('intervalAnnounce', $intervalAnnounce)
      ->save();

    return $this;
  }

}