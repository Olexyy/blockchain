<?php

namespace Drupal\blockchain\Plugin\BlockchainAuth;

use Drupal\blockchain\Entity\BlockchainConfigInterface;
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
    BlockchainAuthInterface,
    ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function authorize(BlockchainRequestInterface $blockchainRequest, BlockchainConfigInterface $blockchainConfig) {

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function addAuthParams(array &$params, BlockchainConfigInterface $blockchainConfig) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {

    return new static($configuration, $plugin_id, $plugin_definition);
  }

}
