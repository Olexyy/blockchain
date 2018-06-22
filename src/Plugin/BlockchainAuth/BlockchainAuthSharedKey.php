<?php

namespace Drupal\blockchain\Plugin\BlockchainAuth;

use Drupal\blockchain\Plugin\BlockchainAuthInterface;
use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\blockchain\Utils\BlockchainRequestInterface;
use Drupal\blockchain\Utils\Util;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * BlockchainAuthSharedKey as simple string.
 *
 * @BlockchainAuth(
 *  id = "shared_key",
 *  label = @Translation("Shared key"),
 * )
 */
class BlockchainAuthSharedKey extends PluginBase implements
  BlockchainAuthInterface, ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * Blockchain service.
   *
   * @var BlockchainServiceInterface
   */
  protected $blockchainService;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, BlockchainServiceInterface $blockchainService) {

    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->blockchainService = $blockchainService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {

    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('blockchain.service')
    );
  }

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

    if (!$authToken = $blockchainRequest->getAuthParam()) {

      return FALSE;
    }
    if (!$this->authIsValid($blockchainRequest->getSelfParam(), $blockchainRequest->getAuthParam())) {

      return FALSE;
    }

    return TRUE;
  }

  /**
   * Setup for auth params.
   *
   * @param array $params
   *   Given params.
   */
  public function addAuthParams(array &$params) {

    $params[BlockchainRequestInterface::PARAM_AUTH] = $this->blockchainService
      ->getConfigService()
      ->tokenGenerate();
  }

  /**
   * Validates auth against current blockchain.
   *
   * @param string $self
   *   Self key.
   * @param string $auth
   *   Auth key.
   *
   * @return bool
   *   Validation result.
   */
  public function authIsValid($self, $auth) {

    $blockchainId = $this->blockchainService
      ->getConfigService()
      ->getCurrentConfig()
      ->getBlockchainId();

    return Util::hash($blockchainId.$self) === $auth;
  }
}
