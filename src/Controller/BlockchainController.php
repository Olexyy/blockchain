<?php

namespace Drupal\blockchain\Controller;


use Drupal\blockchain\Entity\BlockchainConfigInterface;
use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\blockchain\Utils\BlockchainBatchHandler;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Blockchain controller.
 */
class BlockchainController extends ControllerBase {

  /**
   * Blockchain service.
   *
   * @var BlockchainServiceInterface
   */
  protected $blockchainService;

  /**
   * Request stack.
   *
   * @var RequestStack
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
   * @param BlockchainConfigInterface $blockchain_config
   *   Blockchain config object.
   *
   * @return RedirectResponse
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

}
