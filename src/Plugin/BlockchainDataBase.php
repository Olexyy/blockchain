<?php

namespace Drupal\blockchain\Plugin;

use Drupal\blockchain\Entity\BlockchainBlock;
use Drupal\blockchain\Entity\BlockchainBlockInterface;
use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use GuzzleHttp\Client;

/**
 * Base class for Importer plugins.
 */
abstract class BlockchainDataBase extends PluginBase implements
  BlockchainDataInterface, ContainerFactoryPluginInterface {

  /**
   * Blockchain block.
   *
   * @var BlockchainBlock
   */
  protected $blockchainBlock;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * @var RequestStack
   */
  protected $requestStack;

  /**
   * @var LoggerChannelFactory
   */
  protected $loggerFactory;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id,
                              $plugin_definition, EntityTypeManager $entityTypeManager,
                              Client $httpClient, RequestStack $requestStack,
                              LoggerChannelFactory $loggerFactory) {

    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
    $this->httpClient = $httpClient;
    $this->requestStack = $requestStack;
    $this->loggerFactory = $loggerFactory;
    if (isset($configuration[static::BLOCK_KEY]) &&
      $configuration[static::BLOCK_KEY] instanceof BlockchainBlockInterface) {
      $this->blockchainBlock =  $configuration[static::BLOCK_KEY];
    }
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
      $container->get('entity_type.manager'),
      $container->get('http_client'),
      $container->get('request_stack'),
      $container->get('logger.factory')
    );
  }

  /**
   * Getter for logger.
   *
   * @return \Drupal\Core\Logger\LoggerChannelInterface
   *   Logger object.
   */
  protected function getLogger() {
    return $this->loggerFactory->get(self::LOGGER_CHANNEL);
  }

  /**
   * Prepares data before persistence.
   *
   * @param string $data
   *    Raw data.
   *
   * @return string
   *   Prepared data string.
   */
  protected function dataToSleep($data) {

    return $this->getPluginId() . '::' . $data;
  }

  /**
   * Prepares data after reading.
   *
   * @param string $data
   *    Raw data.
   *
   * @return string
   *   Prepared data string.
   */
  protected function dataWakeUp($data) {

    $prefix = $this->getPluginId() . '::';
    if (strpos($data, $prefix) === 0) {
      return substr($data, strlen($prefix)+1, strlen($data));
    }

    return '';
  }

}