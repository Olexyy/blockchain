<?php

namespace Drupal\blockchain\Plugin\BlockchainAuth;

use Drupal\blockchain\Plugin\BlockchainAuthInterface;
use Drupal\blockchain\Utils\BlockchainRequestInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * BlockchainBlockData as simple string.
 *
 * @BlockchainAuth(
 *  id = "none",
 *  label = @Translation("No auth"),
 * )
 */
class BlockchainAuthNone extends PluginBase implements
  BlockchainAuthInterface, ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * Auth handler.
   *
   * @param BlockchainRequestInterface $blockchainRequest
   *   Given request.
   *
   * @return bool
   *   Auth result.
   */
  public function authorize(BlockchainRequestInterface $blockchainRequest) {

    return TRUE;
  }

  /**
   * Setup for auth params.
   *
   * @param array $params
   *   Given params.
   */
  public function addAuthParams(array &$params) { }


  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {

    return new static($configuration, $plugin_id, $plugin_definition);
  }
}
