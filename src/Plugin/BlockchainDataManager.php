<?php

namespace Drupal\blockchain\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Class BlockchainDataManager.
 *
 * @package Drupal\blockchain\Plugin
 */
class BlockchainDataManager extends DefaultPluginManager {

  /**
   * BlockchainDataManager constructor.
   *
   * {@inheritdoc}
   */
  public function __construct(\Traversable $namespaces,
                              CacheBackendInterface $cache_backend,
                              ModuleHandlerInterface $module_handler) {

    parent::__construct('Plugin/BlockchainData', $namespaces, $module_handler,
      'Drupal\blockchain\Plugin\BlockchainDataInterface',
      'Drupal\blockchain\Annotation\BlockchainData');
    $this->alterInfo('blockchain_data_plugin_info');
    $this->setCacheBackend($cache_backend, 'blockchain_data_plugins');
  }
}