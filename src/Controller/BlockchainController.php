<?php

namespace Drupal\blockchain\Controller;

use Drupal\blockchain\Entity\BlockchainConfigInterface;
use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Blockchain controller.
 */
class BlockchainController extends ControllerBase {

  /**
   * Blockchain service.
   *
   * @var \Drupal\blockchain\Service\BlockchainServiceInterface
   */
  protected $blockchainService;

  /**
   * Request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $request;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {

    return new static(
      $container->get('blockchain.service'),
      $container->get('request_stack')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(BlockchainServiceInterface $blockchainService,
                              RequestStack $requestStack) {

    $this->blockchainService = $blockchainService;
    $this->request = $requestStack->getCurrentRequest();
  }

  /**
   * Blocks validation callback.
   *
   * @param \Drupal\blockchain\Entity\BlockchainConfigInterface $blockchain_config
   *   Blockchain config object.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   Response.
   */
  public function storageValidate(BlockchainConfigInterface $blockchain_config) {

    $type = $blockchain_config->id();
    $this->blockchainService->getConfigService()->setCurrentConfig($type);
    if ($this->blockchainService->getStorageService()->checkBlocks()) {
      $this->messenger()->addStatus($this->t('Blocks are valid'));
    }
    else {
      $this->messenger()->addError($this->t('Validation failed'));
    }

    return $this->redirect("entity.{$type}.collection");
  }

  /**
   * Controller callback.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   Response.
   */
  public function discoverConfigs() {

    $count = $this->blockchainService->getConfigService()->discoverBlockchainConfigs();
    $this->messenger()->addStatus($this->t('Discovered @count configurations.', [
      '@count' => $count,
    ]));

    return $this->redirect('entity.blockchain_config.collection');
  }

}
