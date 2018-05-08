<?php

namespace Drupal\blockchain\Plugin\QueueWorker;

use Drupal\blockchain\Entity\BlockchainBlock;
use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\blockchain\Utils\Util;
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
    $blockData = isset($data->blockData) && $data->blockData ? $data->blockData : NULL;
    if (!$blockData) {
      throw new \Exception('Missing block data.');
    }
    if (!$lastBlock = $this->blockchainService->getStorageService()->getLastBlock()) {
      throw new \Exception('Missing generic block.');
    }
    $nonce = $lastBlock->getNonce();
    $newNonce = $this->mine($nonce);
    $block = BlockchainBlock::create();
    $block->setAuthor($this->blockchainService
      ->getConfigService()->getBlockchainNodeId());
    $block->setNonce($newNonce);
    $block->setPreviousHash($lastBlock->getHash());
    $this->blockchainService->getBlockDataHandler($block)->setData($blockData);
    $block->setTimestamp(time());
    $block->save();
    // Announce --->>>>>.
  }

  /**
   *
   *
   * @param $lastNonce
   *   Last nonce.
   *
   * @return string
   */
  protected function mine($lastNonce) {

    $nonce = 0;
    $result = Util::hash($lastNonce.$nonce);
    while (!$this->blockchainService->hashIsValid($result)) {
      $nonce++;
      $result = Util::hash($lastNonce.$nonce);
    }

    return $nonce;
  }
}