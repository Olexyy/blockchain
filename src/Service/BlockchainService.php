<?php

namespace Drupal\blockchain\Service;

use Drupal\blockchain\Entity\BlockchainBlock;
use Drupal\blockchain\Entity\BlockchainBlockInterface;
use Drupal\blockchain\Plugin\BlockchainDataManager;
use Drupal\blockchain\Utils\Util;
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
   * Blockchain data manager.
   *
   * @var BlockchainDataManager
   */
  protected $blockchainDataManager;

  /**
   * BlockchainService constructor.
   *
   * @param BlockchainConfigServiceInterface $blockchainSettingsService
   *   Given service.
   * @param EntityTypeManagerInterface $entityTypeManager
   *   Given service.
   * @param BlockchainDataManager $blockchainDataManager
   *   Given blockchain data manager.
   */
  public function __construct(
    BlockchainConfigServiceInterface $blockchainSettingsService,
    EntityTypeManagerInterface $entityTypeManager,
    BlockchainDataManager $blockchainDataManager) {

    $this->entityTypeManager = $entityTypeManager;
    $this->blockchainServiceSettings = $blockchainSettingsService;
    $this->blockchainDataManager = $blockchainDataManager;
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
  public function getBlockchainBlockCount() {

    return $this->getBlockchainBlockStorage()
      ->getQuery()
      ->accessCheck(FALSE)
      ->count()
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getLastBlockchainBlock() {

    $blockId = $this->getBlockchainBlockStorage()
      ->getQuery()
      ->accessCheck(FALSE)
      ->sort('id', 'DESC')
      ->range(0,1)
      ->execute();
    if ($blockId) {
      return BlockchainBlock::load(current($blockId));
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockchainBlockStorage() {

    try {
      return $this->entityTypeManager->getStorage('blockchain_block');
    } catch (\Exception $exception) {
      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function blockchainIsEmpty() {

    return !$this->getBlockchainBlockCount();
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockchainDataList() {

    $list = [];
    foreach($this->blockchainDataManager->getDefinitions() as $plugin) {
      $list[$plugin['id']] = $plugin['label'];
    }

    return $list;
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