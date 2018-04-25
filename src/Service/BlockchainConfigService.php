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
  public function generateNodeName() {
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

}