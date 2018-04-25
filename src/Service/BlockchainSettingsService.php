<?php

namespace Drupal\blockchain\Service;
use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\workflows\StateInterface;

/**
 * Class BlockchainSettingsService.
 *
 * @package Drupal\blockchain\Service
 */
class BlockchainSettingsService implements BlockchainSettingsServiceInterface {

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
   * BlockchainSettingsService constructor.
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



}