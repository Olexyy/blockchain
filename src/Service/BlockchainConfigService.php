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
  public function getGlobalConfig($editable = FALSE) {

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
  public function getAllowNotSecure() {

    if ($blockchainConfig = $this->getCurrentConfig()) {

      return $blockchainConfig->getAllowNotSecure();
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setAllowNotSecure($allowNotSecure) {

    if ($blockchainConfig = $this->getCurrentConfig()) {
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

    return $this->getCurrentConfig()->setFilterList($blockchainFilterList)->save();
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockchainFilterListAsArray() {

    if ($list = $this->getCurrentConfig()->getFilterList()) {
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

    return Util::hash($this->getCurrentConfig()->getBlockchainId().$this->getCurrentConfig()->getNodeId());
  }

  /**
   * {@inheritdoc}
   */
  public function setCurrentConfig($blockchainConfig) {

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
  public function getCurrentConfig() {

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
    $blockchainConfig->setType(BlockchainConfigInterface::TYPE_SINGLE);
    $blockchainConfig->setIsAuth(FALSE);
    $blockchainConfig->setAllowNotSecure(TRUE);
    $blockchainConfig->setAnnounceManagement(BlockchainConfigInterface::ANNOUNCE_MANAGEMENT_IMMEDIATE);
    $blockchainConfig->setPoolManagement(BlockchainConfigInterface::POOL_MANAGEMENT_MANUAL);
    $blockchainConfig->setDataHandler('raw');
    $blockchainConfig->setPowPosition(BlockchainConfigInterface::POW_POSITION_START);
    $blockchainConfig->setPowExpression('00');
    $blockchainConfig->setIntervalPool(BlockchainConfigInterface::INTERVAL_DEFAULT);
    $blockchainConfig->setIntervalAnnounce(BlockchainConfigInterface::INTERVAL_DEFAULT);
    $blockchainConfig->setFilterType(BlockchainConfigInterface::FILTER_TYPE_BLACKLIST);

    return $blockchainConfig;
  }

  /**
   * {@inheritdoc}
   */
  public function discoverBlockchainConfigs() {

    $count = 0;
    foreach ($this->getBlockchainEntityTypes() as $blockchainEntityType) {
      if (!$this->exists($blockchainEntityType)) {
        $blockchainConfig = $this->getDefaultBlockchainConfig($blockchainEntityType);
        if ($this->save($blockchainConfig)) {
          $count++;
        }
      }
    }

    return $count;
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
   * {@inheritdoc}
   */
  public function exists($blockchainConfigId) {

    return (bool) BlockchainConfig::load($blockchainConfigId);
  }

  /**
   * {@inheritdoc}
   */
  public function getAllConfigs() {

    return BlockchainConfig::loadMultiple();
  }

}
