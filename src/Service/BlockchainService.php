<?php

namespace Drupal\blockchain\Service;


use Drupal\blockchain\Entity\BlockchainBlock;
use Drupal\Core\Entity\EntityTypeManagerInterface;

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
   * Entity type manager.
   *
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * BlockchainService constructor.
   *
   * @param BlockchainConfigServiceInterface $blockchainSettingsService
   *   Given service.
   * @param EntityTypeManagerInterface $entityTypeManager
   *   Given service.
   */
  public function __construct(
    BlockchainConfigServiceInterface $blockchainSettingsService,
    EntityTypeManagerInterface $entityTypeManager) {

    $this->entityTypeManager = $entityTypeManager;
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
    $block->setHash('000');
    $block->setTimestamp(time());
    $block->setData('Generic block');
    $block->setAuthor('me');
    return $block;
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockchainBlockCount() {
    return $this->getBlockchainBlockStorage()
      ->getQuery()
      ->count()
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockchainBlockStorage() {
    try {
      return $this->entityTypeManager->getStorage('blockchain_block');
    }
    catch (\Exception $exception) {
      return NULL;
    }
  }

}