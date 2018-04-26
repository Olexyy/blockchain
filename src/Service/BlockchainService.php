<?php

namespace Drupal\blockchain\Service;


use Drupal\blockchain\Entity\BlockchainBlock;

/**
 * Class BlockchainService.
 *
 * @package Drupal\blockchain\Service
 */
class BlockchainService implements BlockchainServiceInterface {

  /**
   * BlockchainSettingsService.
   *
   * @var BlockchainConfigServiceInterface
   */
  protected $blockchainServiceSettings;

  /**
   * BlockchainService constructor.
   *
   * @param BlockchainConfigServiceInterface $blockchainSettingsService
   *   Given service.
   */
  public function __construct(BlockchainConfigServiceInterface $blockchainSettingsService) {

    $this->blockchainServiceSettings = $blockchainSettingsService;

  }

  /**
   * {@inheritdoc}
   */
  public function getConfigService() {
    return $this->blockchainServiceSettings;
  }

  /**
   * {@inheritdoc}
   */
  public function getGenericBlock() {
    $block = BlockchainBlock::create();
    $block->setHash(000);
    $block->setTimestamp(time());
    $block->setData('Generic block');
    $block->setAuthor('me');
    return $block;
  }

}