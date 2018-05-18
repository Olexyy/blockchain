<?php

namespace Drupal\blockchain\Controller;

use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\blockchain\Utils\BlockchainRequestInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

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
  protected $requestStack;

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
    $this->requestStack = $requestStack;
  }

  /**
   * Subscribe action.
   *
   * @return JsonResponse
   */
  public function subscribe() {

    $result = $this->validate(BlockchainRequestInterface::TYPE_SUBSCRIBE);
    if ($result instanceof Response) {
      return $result;
    }
    elseif ($result instanceof BlockchainRequestInterface) {
      // implement business logic...
      return JsonResponse::create(['message' => 'Success'], 200);
    }

    return JsonResponse::create(['message' => 'Server error'], 505);
  }

  /**
   * Request validator.
   *
   * This validates request according to defined protocol
   * and returns JsonResponse in case of fail or BlockchainRequest
   * in case if request is valid.
   *
   * @param string $type
   *   Type of operation.
   *
   * @return JsonResponse|BlockchainRequestInterface
   *   Execution result.
   */
  public function validate($type) {

    return $this->blockchainService
      ->getValidatorService()
      ->validateRequest($type, $this->requestStack->getCurrentRequest());
  }

}
