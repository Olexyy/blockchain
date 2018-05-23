<?php

namespace Drupal\blockchain\Controller;

use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\blockchain\Utils\BlockchainRequestInterface;
use Drupal\blockchain\Utils\BlockchainResponse;
use Drupal\blockchain\Utils\BlockchainResponseInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    $logger = $this->getLogger('blockchain.api');
    $logger->info('Subscribe attempt initiated.');
    $result = $this->validate(BlockchainRequestInterface::TYPE_SUBSCRIBE);
    if ($result instanceof BlockchainResponseInterface) {

      return $result->log($logger)->toJsonResponse();
    }
    elseif ($result instanceof BlockchainRequestInterface) {
      if (!$this->blockchainService->getNodeService()->exists($result->getSelfParam())) {
        if ($this->blockchainService->getNodeService()->createFromRequest($result)) {

          return BlockchainResponse::create()
            ->setIp($result->getIp())
            ->setPort($result->getPort())
            ->setStatusCode(200)
            ->setMessageParam('Success')
            ->setDetailsParam('Added to list.')
            ->log($logger)
            ->toJsonResponse();
        }
      }
      else {

        return BlockchainResponse::create()
          ->setIp($result->getIp())
          ->setPort($result->getPort())
          ->setStatusCode(406)
          ->setMessageParam('Not acceptable')
          ->setDetailsParam('Already in list.')
          ->log($logger)
          ->toJsonResponse();
      }
    }

    return BlockchainResponse::create()
      ->setIp($result->getIp())
      ->setPort($result->getPort())
      ->setStatusCode(505)
      ->setMessageParam('Server error')
      ->setDetailsParam('Something unexpected happened.')
      ->log($logger)
      ->toJsonResponse();
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
   * @return BlockchainResponseInterface|BlockchainRequestInterface
   *   Execution result.
   */
  public function validate($type) {

    return $this->blockchainService
      ->getValidatorService()
      ->validateRequest($type, $this->requestStack->getCurrentRequest());
  }

}
