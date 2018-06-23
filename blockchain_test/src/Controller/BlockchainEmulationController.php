<?php

namespace Drupal\blockchain_emulation\Controller;


use Drupal\blockchain\Controller\BlockchainController;
use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\blockchain_emulation\Service\BlockchainEmulationStorageServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Blockchain controller.
 */
class BlockchainEmulationController extends BlockchainController {

  /**
   * Blockchain emulation storage.
   *
   * @var BlockchainEmulationStorageServiceInterface
   */
  protected $blockchainEmulationStorageService;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {

    return new static(
      $container->get('blockchain.service'),
      $container->get('request_stack'),
      $container->get('blockchain.emulation.storage')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(BlockchainServiceInterface $blockchainService,
                              RequestStack $requestStack,
                              BlockchainEmulationStorageServiceInterface $blockchainEmulationStorageService) {

    parent::__construct($blockchainService, $requestStack);
    $this->blockchainEmulationStorageService = $blockchainEmulationStorageService;
    $this->blockchainBlockStorage = $this->blockchainEmulationStorageService;
  }

}
