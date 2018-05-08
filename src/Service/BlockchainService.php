<?php

namespace Drupal\blockchain\Service;

use Drupal\blockchain\Entity\BlockchainBlock;
use Drupal\blockchain\Entity\BlockchainBlockInterface;
use Drupal\blockchain\Plugin\BlockchainDataManager;
use Drupal\blockchain\Utils\Util;

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
   * Blockchain data manager.
   *
   * @var BlockchainDataManager
   */
  protected $blockchainDataManager;

  /**
   * Blockchain storage service.
   *
   * @var BlockchainStorageServiceInterface
   */
  protected $blockchainStorageService;

  /**
   * BlockchainService constructor.
   *
   * @param BlockchainConfigServiceInterface $blockchainSettingsService
   *   Given service.
   * @param BlockchainStorageServiceInterface $blockchainStorageService
   *   Given service.
   * @param BlockchainDataManager $blockchainDataManager
   *   Given blockchain data manager.
   */
  public function __construct(
    BlockchainConfigServiceInterface $blockchainSettingsService,
    BlockchainStorageServiceInterface $blockchainStorageService,
    BlockchainDataManager $blockchainDataManager) {

    $this->blockchainServiceSettings = $blockchainSettingsService;
    $this->blockchainDataManager = $blockchainDataManager;
    $this->blockchainStorageService = $blockchainStorageService;
  }

  /**
   * {@inheritdoc}
   */
  public function getStorageService() {
    return $this->blockchainStorageService;
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
    $block->setHash(Util::hash('111'));
    $block->setTimestamp(time());
    $block->setNonce('111');
    $block->setAuthor($this->getConfigService()->getBlockchainNodeId());
    $this->getBlockchainDataHandler($block)->setData('Generic block.');

    return $block;
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockchainDataManager() {
    return $this->blockchainDataManager;
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockchainDataHandler(BlockchainBlockInterface $block) {

    $pluginId = $this->getConfigService()->getConfig()->get('dataHandler');
    try {
      return $this->blockchainDataManager->createInstance($pluginId, [
        'blockchainBlock' => $block,
      ]);
    } catch (\Exception $exception) {
      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function hashIsValid($hash) {

    $powPosition = $this->getConfigService()->getPowPosition();
    $powExpression = $this->getConfigService()->getPowExpression();
    $length = strlen($powExpression);
    if ($powPosition === BlockchainConfigServiceInterface::POW_POSITION_START) {
      if (substr($hash, 0, $length) === $powExpression) {
        return TRUE;
      }
    }
    else {
      if (substr($hash, -$length) === $powExpression) {
        return TRUE;
      }
    }

    return FALSE;
  }

}