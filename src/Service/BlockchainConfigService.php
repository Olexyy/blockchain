<?php

namespace Drupal\blockchain\Service;

use Drupal\blockchain\Entity\BlockchainConfig;
use Drupal\blockchain\Entity\BlockchainConfigInterface;
use Drupal\blockchain\Utils\Util;
use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
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
   * Entity type manager.
   *
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Blockchain config context.
   *
   * @var BlockchainConfigInterface $blockchainConfig
   */
  protected static $blockchainConfig;

  /**
   * BlockchainConfigServiceInterface constructor.
   *
   * {@inheritdoc}
   */
  public function __construct(UuidInterface $uuid,
                              StateInterface $state,
                              ConfigFactoryInterface $configFactory,
                              EntityTypeManagerInterface $entityTypeManager) {

    $this->uuid = $uuid;
    $this->state = $state;
    $this->configFactory = $configFactory;
    $this->entityTypeManager = $entityTypeManager;
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

    if (!($blockchainType = $this->getConfig()->get('blockchainType'))) {
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

    if (!($poolManagement = $this->getConfig()->get('poolManagement'))) {
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

    if (!($announceManagement = $this->getConfig()->get('announceManagement'))) {
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

    if (!($powPosition = $this->getConfig()->get('powPosition'))) {
      $powPosition = static::POW_POSITION_START;
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

    if (!($powExpression = $this->getConfig()->get('powExpression'))) {
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

    if (!($intervalPool = $this->getConfig()->get('intervalPool'))) {
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

    if (!($intervalAnnounce = $this->getConfig()->get('intervalAnnounce'))) {
      $intervalAnnounce = static::INTERVAL_DEFAULT;
      $this->setIntervalAnnounce($intervalAnnounce);
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

  /**
   * {@inheritdoc}
   */
  public function isBlockchainAuth() {

    return $this->getConfig()->get('blockchainAuth');
  }

  /**
   * {@inheritdoc}
   */
  public function setBlockchainAuth($blockchainAuth) {

    $this->getConfig(TRUE)
      ->set('blockchainAuth', $blockchainAuth)
      ->save();

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockchainFilterType() {

    if (!($blockchainFilterType = $this->getConfig()->get('blockchainFilterType'))) {
      $blockchainFilterType = static::FILTER_TYPE_BLACKLIST;
      $this->setBlockchainFilterType($blockchainFilterType);
    }

    return $blockchainFilterType;
  }

  /**
   * {@inheritdoc}
   */
  public function setBlockchainFilterType($blockchainFilterType) {

    $this->getConfig(TRUE)
      ->set('blockchainFilterType', $blockchainFilterType)
      ->save();

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockchainFilterList() {

    return $this->getConfig()->get('blockchainFilterList');
  }

  /**
   * {@inheritdoc}
   */
  public function setBlockchainFilterList($blockchainFilterList) {

    $this->getConfig(TRUE)
      ->set('blockchainFilterList', $blockchainFilterList)
      ->save();

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getAllowNotSecure() {
    if (($allowNotSecure = $this->getConfig()->get('allowNotSecure')) === NULL) {
      $allowNotSecure = TRUE;
      $this->setAllowNotSecure($allowNotSecure);
    }
    return $this->getConfig()->get('allowNotSecure');
  }

  /**
   * {@inheritdoc}
   */
  public function setAllowNotSecure($allowNotSecure) {

    $this->getConfig(TRUE)
      ->set('allowNotSecure', $allowNotSecure)
      ->save();

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setBlockchainFilterListAsArray(array $blockchainFilterList) {

    $blockchainFilterList = implode("\r\n", $blockchainFilterList);
    $this->setBlockchainFilterList($blockchainFilterList);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockchainFilterListAsArray() {

    if ($list = $this->getBlockchainFilterList()) {
      $parsed = preg_split('~\R~', $list);
      array_walk($parsed, 'trim');
      return $parsed;
    }

    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function tokenGenerate() {

    return Util::hash($this->getBlockchainId().$this->getBlockchainNodeId());
  }

  /**
   * {@inheritdoc}
   */
  public function setCurrentBlockchainConfig($blockchainConfig) {

    if ($blockchainConfig instanceof BlockchainConfigInterface) {
      static::$blockchainConfig = $blockchainConfig;

      return TRUE;
    }
    elseif (is_string($blockchainConfig)) {
      if ($blockchainConfigEntity = BlockchainConfig::load($blockchainConfig)) {
        static::$blockchainConfig = $blockchainConfigEntity;

        return TRUE;
      }
      else {
        $blockchainEntityTypes = $this->getBlockchainEntityTypes();
        if (in_array($blockchainConfig, $blockchainEntityTypes)) {
          $blockchainConfigEntity = $this->getDefaultBlockchainConfig($blockchainConfig);
          $blockchainConfigEntity->save();
          static::$blockchainConfig = $blockchainConfigEntity;

          return TRUE;
        }
      }
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrentBlockchainConfig() {

    if (static::$blockchainConfig) {

      return static::$blockchainConfig;
    }

    return NULL;
  }

  /**
   * Creates config with default settings for entity id.
   *
   * @param string $entityTypeId
   *    Entity type id.
   *
   * @return BlockchainConfig|\Drupal\Core\Entity\EntityInterface
   */
  public function getDefaultBlockchainConfig($entityTypeId) {

    $blockchainConfig = BlockchainConfig::create([]);
    $blockchainConfig->setId($entityTypeId);
    $blockchainConfig->setLabel($entityTypeId);
    $blockchainConfig->setBlockchainId($this->generateId());
    $blockchainConfig->setNodeId($this->generateId());
    $blockchainConfig->setType(static::TYPE_SINGLE);
    $blockchainConfig->setIsAuth(FALSE);
    $blockchainConfig->setAllowNotSecure(TRUE);
    $blockchainConfig->setAnnounceManagement(static::ANNOUNCE_MANAGEMENT_IMMEDIATE);
    $blockchainConfig->setPoolManagement(static::POOL_MANAGEMENT_MANUAL);
    $blockchainConfig->setDataHandler('raw');
    $blockchainConfig->setPowPosition(static::POW_POSITION_START);
    $blockchainConfig->setPowExpression('00');
    $blockchainConfig->setIntervalPool(static::INTERVAL_DEFAULT);
    $blockchainConfig->setIntervalAnnounce(static::INTERVAL_DEFAULT);
    $blockchainConfig->setFilterType(static::FILTER_TYPE_BLACKLIST);

    return $blockchainConfig;
  }

  public function getBlockchainEntityTypes() {

    $blockchainEntityTypes = [];
    foreach ($this->entityTypeManager->getDefinitions() as $definition) {
      if ($additional = $definition->get('additional')) {
        if (isset($additional['blockchain_entity']) && $additional['blockchain_entity']) {
          $blockchainEntityTypes[]= $definition->id();
        }
      }
    }

    return $blockchainEntityTypes;
  }

}
