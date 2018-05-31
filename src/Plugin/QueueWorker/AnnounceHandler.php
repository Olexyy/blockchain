<?php

namespace Drupal\blockchain\Plugin\QueueWorker;

use Drupal\blockchain\Entity\BlockchainBlock;
use Drupal\blockchain\Plugin\BlockchainDataInterface;
use Drupal\blockchain\Service\BlockchainQueueServiceInterface;
use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\blockchain\Utils\BlockchainRequest;
use Drupal\blockchain\Utils\Util;
use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Processes announce handling.
 *
 * @QueueWorker(
 * id = "announce_queue",
 * title = @Translation("Announce queue handler."),
 * )
 */
class AnnounceHandler extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  const LOGGER_CHANNEL = 'announce_handler';

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
  public function __construct(array $configuration,
                              $plugin_id,
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
    $announceData = property_exists($data, BlockchainQueueServiceInterface::ANNOUNCE_QUEUE_ITEM) ?
      $data->{BlockchainQueueServiceInterface::ANNOUNCE_QUEUE_ITEM} : NULL;
    if (!$announceData) {
      throw new \Exception('Missing announce data.');
    }
    if (!($blockchainRequest = BlockchainRequest::wakeup($announceData))) {
      throw new \Exception('Invalid announce queue data.');
    }
    $blockchainNode = $this->blockchainService->getNodeService()->load($blockchainRequest->getSelfParam());
    if (!($blockchainNode)) {
      throw new \Exception('Invalid announce request data.');
    }
    $endPoint = $blockchainNode->getEndPoint();
    // Here we for now handle only not conflicting part.
    // Plan to fetch to cache if are conflicts...
    // LOCK for concurrent updates. FINALLY release.
    // Aim is one update to be consistent.
    $result = $this->blockchainService->getApiService()
      ->executeCount($endPoint, $this->blockchainService->getStorageService()->getLastBlock());
    while ($result->getCountParam() > $this->blockchainService->getStorageService()->getBlockCount()) {
      $result = $this->blockchainService->getApiService()
        ->executeSync($endPoint, $this->blockchainService->getStorageService()->getLastBlock());
    }
    // finally release block....
  }

  /**
   * Mining procedure.
   *
   * @param string $miningString
   *   Given value.
   *
   * @return string
   */
  protected function mine($miningString) {

    $nonce = 0;
    $result = Util::hash($miningString.$nonce);
    $validator = $this->blockchainService->getValidatorService();
    while (!$validator->hashIsValid($result)) {
      $nonce++;
      $result = Util::hash($miningString.$nonce);
    }

    return $nonce;
  }
}