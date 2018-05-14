<?php

namespace Drupal\blockchain\Controller;

use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

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
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('blockchain.service'));
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(BlockchainServiceInterface $blockchainService) {

    $this->blockchainService = $blockchainService;
  }

  /**
   * Subscribe action.
   *
   * @return JsonResponse
   */
  public function subscribe() {

    $ip = $this->blockchainService->getApiService()->getCurrentRequest()->getClientIp();
    $id = $this->blockchainService->getApiService()->getIp();

    return JsonResponse::create([
      'message' => 'Success'], 200);
  }

  public function validate() {

  }

  /**
   * Access handler method.
   *
   * @param AccountInterface $account
   *   User account.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   Access check result.
   */
  public function access(AccountInterface $account) {

    return AccessResult::allowed();
  }
}