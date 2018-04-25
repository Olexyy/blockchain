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
   * @var BlockchainSettingsServiceInterface
   */
  protected $blockchainServiceSettings;

  /**
   * BlockchainService constructor.
   *
   * @param BlockchainSettingsServiceInterface $blockchainSettingsService
   *   Given service.
   */
  public function __construct(BlockchainSettingsServiceInterface $blockchainSettingsService) {

    $this->blockchainServiceSettings = $blockchainSettingsService;

  }

  /**
   * Getter for settings service.
   *
   * @return BlockchainSettingsServiceInterface
   */
  public function getSettings() {
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