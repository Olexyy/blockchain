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

    if ($blockchainConfig = $this->getCurrentBlockchainConfig()) {

      return $blockchainConfig->getBlockchainId();
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockchainNodeId() {

    if ($blockchainConfig = $this->getCurrentBlockchainConfig()) {

      return $blockchainConfig->getNodeId();
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setBlockchainId($blockchainId = NULL) {

    if ($blockchainConfig = $this->getCurrentBlockchainConfig()) {
      $blockchainConfig->setBlockchainId($blockchainId);

      return $this->save($blockchainConfig);
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function setBlockchainNodeId($blockchainNodeId = NULL) {

    if ($blockchainConfig = $this->getCurrentBlockchainConfig()) {
      $blockchainConfig->setBlockchainId($blockchainNodeId);

      return $this->save($blockchainConfig);
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockchainType() {

    if ($blockchainConfig = $this->getCurrentBlockchainConfig()) {

      return $blockchainConfig->getType();
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setBlockchainType($blockchainType) {

    if ($blockchainConfig = $this->getCurrentBlockchainConfig()) {
      $blockchainConfig->setType($blockchainType);

      return $this->save($blockchainConfig);
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getPoolManagement() {

    if ($blockchainConfig = $this->getCurrentBlockchainConfig()) {

      return $blockchainConfig->getPoolManagement();
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setPoolManagement($poolManagement) {

    if ($blockchainConfig = $this->getCurrentBlockchainConfig()) {
      $blockchainConfig->setPoolManagement($poolManagement);

      return $this->save($blockchainConfig);
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getAnnounceManagement() {

    if ($blockchainConfig = $this->getCurrentBlockchainConfig()) {

      return $blockchainConfig->getAnnounceManagement();
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setAnnounceManagement($announceManagement) {

    if ($blockchainConfig = $this->getCurrentBlockchainConfig()) {
      $blockchainConfig->setAnnounceManagement($announceManagement);

      return $this->save($blockchainConfig);
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getPowPosition() {

    if ($blockchainConfig = $this->getCurrentBlockchainConfig()) {

      return $blockchainConfig->getPowPosition();
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setPowPosition($powPosition) {

    if ($blockchainConfig = $this->getCurrentBlockchainConfig()) {
      $blockchainConfig->setPowPosition($powPosition);

      return $this->save($blockchainConfig);
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getPowExpression() {

    if ($blockchainConfig = $this->getCurrentBlockchainConfig()) {

      return $blockchainConfig->getPowExpression();
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setPowExpression($powExpression) {

    if ($blockchainConfig = $this->getCurrentBlockchainConfig()) {
      $blockchainConfig->setPowExpression($powExpression);

      return $this->save($blockchainConfig);
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getIntervalPool() {

    if ($blockchainConfig = $this->getCurrentBlockchainConfig()) {

      return $blockchainConfig->getIntervalPool();
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setIntervalPool($intervalPool) {

    if ($blockchainConfig = $this->getCurrentBlockchainConfig()) {
      $blockchainConfig->setIntervalPool($intervalPool);

      return $this->save($blockchainConfig);
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getIntervalAnnounce() {

    if ($blockchainConfig = $this->getCurrentBlockchainConfig()) {

      return $blockchainConfig->getIntervalAnnounce();
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setIntervalAnnounce($intervalAnnounce) {

    if ($blockchainConfig = $this->getCurrentBlockchainConfig()) {
      $blockchainConfig->setIntervalAnnounce($intervalAnnounce);

      return $this->save($blockchainConfig);
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function isBlockchainAuth() {

    if ($blockchainConfig = $this->getCurrentBlockchainConfig()) {

      return $blockchainConfig->getIsAuth();
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setBlockchainAuth($blockchainAuth) {

    if ($blockchainConfig = $this->getCurrentBlockchainConfig()) {
      $blockchainConfig->setIsAuth($blockchainAuth);

      return $this->save($blockchainConfig);
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockchainFilterType() {

    if ($blockchainConfig = $this->getCurrentBlockchainConfig()) {

      return $blockchainConfig->getFilterType();
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setBlockchainFilterType($blockchainFilterType) {

    if ($blockchainConfig = $this->getCurrentBlockchainConfig()) {
      $blockchainConfig->setFilterType($blockchainFilterType);

      return $this->save($blockchainConfig);
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockchainFilterList() {

    if ($blockchainConfig = $this->getCurrentBlockchainConfig()) {

      return $blockchainConfig->getFilterList();
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setBlockchainFilterList($blockchainFilterList) {

    if ($blockchainConfig = $this->getCurrentBlockchainConfig()) {
      $blockchainConfig->setFilterList($blockchainFilterList);

      return $this->save($blockchainConfig);
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getAllowNotSecure() {

    if ($blockchainConfig = $this->getCurrentBlockchainConfig()) {

      return $blockchainConfig->getAllowNotSecure();
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setAllowNotSecure($allowNotSecure) {

    if ($blockchainConfig = $this->getCurrentBlockchainConfig()) {
      $blockchainConfig->setAllowNotSecure($allowNotSecure);

      return $this->save($blockchainConfig);
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function setBlockchainFilterListAsArray(array $blockchainFilterList) {

    $blockchainFilterList = implode("\r\n", $blockchainFilterList);

    return $this->setBlockchainFilterList($blockchainFilterList);
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
      if (!$this->exists($blockchainConfig->id())) {
        static::$blockchainConfig = $blockchainConfig;

        return TRUE;
      }
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
          if ($this->save($blockchainConfigEntity)) {
            static::$blockchainConfig = $blockchainConfigEntity;

            return TRUE;
          }
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
   * {@inheritdoc}
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

  /**
   * {@inheritdoc}
   */
  public function discoverBlockchainConfigs() {
    foreach ($this->getBlockchainEntityTypes() as $blockchainEntityType) {
      if (!$this->exists($blockchainEntityType)) {
        $blockchainConfig = $this->getDefaultBlockchainConfig($blockchainEntityType);
        $this->save($blockchainConfig);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
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

  /**
   * {@inheritdoc}
   */
  public function save(BlockchainConfigInterface $blockchainConfig) {

    try {
      $this->entityTypeManager
        ->getStorage(BlockchainConfigInterface::ENTITY_TYPE)
        ->save($blockchainConfig);

      return TRUE;
    } catch (\Exception $exception) {

      return FALSE;
    }
  }

  /**
   * Predicate defines if config exists.
   *
   * @param string $blockchainConfigId
   *   Given id.
   *
   * @return bool
   *   Test result.
   */
  public function exists($blockchainConfigId) {

    return (bool) BlockchainConfig::load($blockchainConfigId);
  }

}
