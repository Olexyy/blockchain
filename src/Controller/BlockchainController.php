<?php

namespace Drupal\blockchain\Controller;

use Drupal\blockchain\Service\BlockchainConfigServiceInterface;
use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\blockchain\Utils\BlockchainRequest;
use Drupal\blockchain\Utils\BlockchainRequestInterface;
use Drupal\Core\Controller\ControllerBase;
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
    return new static(
      $container->get('blockchain.service')
    );
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
    $blockchainRequest = BlockchainRequest::createFromRequest(
      $this->blockchainService->getApiService()->getCurrentRequest(),
      BlockchainRequestInterface::TYPE_SUBSCRIBE
    );

    $ip = $this->blockchainService->getApiService()->getCurrentRequest()->getClientIp();
    $id = $this->blockchainService->getApiService()->getIp();

    return JsonResponse::create(['message' => 'Success'], 200);
  }

  /**
   * Request validator.
   *
   * @return JsonResponse|void
   */
  public function validate() {

    $configService = $this->blockchainService->getConfigService();
     if ($configService->getBlockchainType() === BlockchainConfigServiceInterface::TYPE_SINGLE
       || !$configService->isBlockchainAuth()) {
       return JsonResponse::create(['message' => 'Forbidden'], 403);
     }

  }

}
