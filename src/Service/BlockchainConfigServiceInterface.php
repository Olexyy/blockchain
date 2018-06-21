<?php

namespace Drupal\blockchain\Service;

use Drupal\blockchain\Entity\BlockchainConfigInterface;
use Drupal\Core\Config\Config;

/**
 * Interface BlockchainConfigServiceInterface.
 *
 * @package Drupal\blockchain\Service
 */
interface BlockchainConfigServiceInterface {

  /**
   * Getter for unique identifier.
   *
   * @return string
   *   Unique identifier.
   */
  function generateId();

  /**
   * Getter for config.
   *
   * @param bool $editable
   *   Defines result.
   *
   * @return \Drupal\Core\Config\Config|\Drupal\Core\Config\ImmutableConfig|Config
   */
  function getGlobalConfig($editable = FALSE);

  /**
   * State values.
   *
   * @return array
   *   Values for given state.
   */
  public function getState();

  /**
   * Setter for blockchain nodes filter list.
   *
   * @param array $blockchainFilterList
   *   Given value.
   *
   * @return $this
   *   Chaining.
   */
  public function setBlockchainFilterListAsArray(array $blockchainFilterList);

  /**
   * Getter for blockchain nodes filter list.
   *
   * @return string[]
   *   Array of values.
   */
  public function getBlockchainFilterListAsArray();

  /**
   * Generates auth token.
   *
   * @return string
   *   Hash.
   */
  public function tokenGenerate();

  /**
   * Getter for allow not secure protocol.
   *
   * @return bool
   *   Value.
   */
  public function getAllowNotSecure();

  /**
   * Setter for allow not secure protocol.
   *
   * @param $allowNotSecure
   *    Given value.
   *
   * @return $this
   *   Chaining.
   */
  public function setAllowNotSecure($allowNotSecure);

  /**
   * Setter for blockchain config.
   *
   * After this action service is aware of blockchain type and
   * settings to use.
   *
   * @param BlockchainConfigInterface|string $blockchainConfig
   *   Blockchain config of id.
   *
   * @return bool
   *   Execution result.
   */
  public function setCurrentConfig($blockchainConfig);

  /**
   * Getter for blockchain config.
   *
   * @return BlockchainConfigInterface|null
   *   Returns config entity if any.
   */
  public function getCurrentConfig();

  /**
   * Creates config with default settings for entity id.
   *
   * @param string $entityTypeId
   *    Entity type id.
   *
   * @return BlockchainConfigInterface|\Drupal\Core\Entity\EntityInterface
   */
  public function getDefaultBlockchainConfig($entityTypeId);

  /**
   * Save handler.
   *
   * @param BlockchainConfigInterface $blockchainConfig
   *   Given entity.
   *
   * @return bool
   *   Execution result.
   */
  public function save(BlockchainConfigInterface $blockchainConfig);

  /**
   * Predicate defines if config exists.
   *
   * @param string $blockchainConfigId
   *   Given id.
   *
   * @return bool
   *   Test result.
   */
  public function exists($blockchainConfigId);

  /**
   * Helper to get blockchain entity types.
   *
   * @return array|string[]
   *   Array of entity type ids.
   */
  public function getBlockchainEntityTypes();

  /**
   * Handler to discover and save blockchain configs.
   *
   * @return int
   *   Count of discovered configs.
   */
  public function discoverBlockchainConfigs();

  /**
   * Handler to get list of blockchain configs.
   *
   * @return BlockchainConfigInterface[]|array
   *   Array of entities if any.
   */
  public function getAllConfigs();

}