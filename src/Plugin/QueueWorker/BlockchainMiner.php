<?php

namespace Drupal\blockchain\Plugin\QueueWorker;

use Drupal\blockchain\Entity\BlockchainBlock;
use Drupal\blockchain\Plugin\BlockchainDataInterface;
use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\blockchain\Utils\BlockchainRequestInterface;
use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Processes import.
 *
 * @QueueWorker(
 * id = "blockchain_pool",
 * title = @Translation("Blockchain pool worker."),
 * )
 */
class BlockchainMiner extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  const LOGGER_CHANNEL = 'blockchain_pool';

  /**
   * Logger service.
   *
   * @var LoggerChannelFactory
   */
  protected $loggerFactory;

  /**
   * Blockchain service.
   *
   * @var BlockchainServiceInterface
   */
  protected $blockchainService;

  /**
   * Constructs a ImporterQueue worker.
   *
   * @param array $configuration
   *   Configuration array.
   * @param string $plugin_id
   *   Plugin id.
   * @param mixed $plugin_definition
   *   Plugin definition.
   * @param LoggerChannelFactory $loggerFactory
   *   Logger factory.
   * @param BlockchainServiceInterface $blockchainService
   *   Blockchain service.
   */
  public function __construct(array $configuration, $plugin_id,
                              $plugin_definition,
                              LoggerChannelFactory $loggerFactory,
                              BlockchainServiceInterface $blockchainService) {

    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->loggerFactory = $loggerFactory;
    $this->blockchainService = $blockchainService;
  }
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array
  $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('logger.factory'),
      $container->get('blockchain.service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    $blockData = property_exists($data, BlockchainDataInterface::DATA_KEY) ?
      $data->{BlockchainDataInterface::DATA_KEY} : NULL;
    if (!$blockData) {
      throw new \Exception('Missing block data.');
    }
    if (!$this->blockchainService->getDataManager()->extractPluginId($blockData)) {
      throw new \Exception('Invalid data handler plugin id.');
    }
    if (!$lastBlock = $this->blockchainService->getStorageService()->getLastBlock()) {
      throw new \Exception('Missing generic block.');
    }
    $block = BlockchainBlock::create();
    $block->setPreviousHash($lastBlock->getHash());
    $block->setData($blockData);
    $block->setAuthor($this->blockchainService
      ->getConfigService()->getBlockchainNodeId());
    $block->setTimestamp(time());
    $this->blockchainService->getMinerService()->mineBlock($block);
    $block->save();
    $this->blockchainService->getApiService()->executeAnnounce([
      BlockchainRequestInterface::PARAM_COUNT => $this->blockchainService->getStorageService()->getBlockCount(),
    ]);
  }

}
