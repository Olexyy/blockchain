<?php

namespace Drupal\blockchain\Controller;

use Drupal\blockchain\Service\BlockchainConfigServiceInterface;
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
   * Announce action.
   *
   * @return JsonResponse
   *   Response by convention.
   */
  public function announce() {

    $logger = $this->getLogger('blockchain.api');
    $logger->info('Announce attempt initiated.');
    $result = $this->validate(BlockchainRequestInterface::TYPE_ANNOUNCE);
    if ($result instanceof BlockchainResponseInterface) {

      return $result->log($logger)->toJsonResponse();
    }
    elseif ($result instanceof BlockchainRequestInterface) {
      if ($result->hasCountParam()) {
        $ownBlockCount = $this->blockchainService->getStorageService()->getBlockCount();
        if ($ownBlockCount < $result->getCountParam()) {
          $this->blockchainService->getQueueService()->addAnnounceItem($result->sleep());
          $announceManagement = $this->blockchainService->getConfigService()->getAnnounceManagement();
          if ($announceManagement == BlockchainConfigServiceInterface::ANNOUNCE_MANAGEMENT_IMMEDIATE) {
            $this->blockchainService->getQueueService()->doAnnounceHandling();
          }

          return BlockchainResponse::create()
            ->setIp($result->getIp())
            ->setPort($result->getPort())
            ->setSecure($result->isSecure())
            ->setStatusCode(200)
            ->setMessageParam('Success')
            ->setDetailsParam('Added to queue.')
            ->log($logger)
            ->toJsonResponse();
        }
        else {

          return BlockchainResponse::create()
            ->setIp($result->getIp())
            ->setPort($result->getPort())
            ->setSecure($result->isSecure())
            ->setStatusCode(406)
            ->setMessageParam('Not acceptable')
            ->setDetailsParam('Count of blocks is less or equals.')
            ->log($logger)
            ->toJsonResponse();
        }

      }
      else {

        return BlockchainResponse::create()
          ->setIp($result->getIp())
          ->setPort($result->getPort())
          ->setSecure($result->isSecure())
          ->setStatusCode(400)
          ->setMessageParam('Bad request')
          ->setDetailsParam('No count param.')
          ->log($logger)
          ->toJsonResponse();
      }
    }

    return BlockchainResponse::create()
      ->setIp($result->getIp())
      ->setPort($result->getPort())
      ->setSecure($result->isSecure())
      ->setStatusCode(505)
      ->setMessageParam('Server error')
      ->setDetailsParam('Something unexpected happened.')
      ->log($logger)
      ->toJsonResponse();
  }

  /**
   * Subscribe action.
   *
   * @return JsonResponse
   *   Response by convention.
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
            ->setSecure($result->isSecure())
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
          ->setSecure($result->isSecure())
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
      ->setSecure($result->isSecure())
      ->setStatusCode(505)
      ->setMessageParam('Server error')
      ->setDetailsParam('Something unexpected happened.')
      ->log($logger)
      ->toJsonResponse();
  }

  /**
   * Count action.
   *
   * @return JsonResponse
   *   Response by convention.
   */
  public function count() {

    $logger = $this->getLogger('blockchain.api');
    $logger->info('Subscribe attempt initiated.');
    $result = $this->validate(BlockchainRequestInterface::TYPE_GET_COUNT);
    if ($result instanceof BlockchainResponseInterface) {

      return $result->log($logger)->toJsonResponse();
    }
    elseif ($result instanceof BlockchainRequestInterface) {

      $blockCount = $this->blockchainService->getStorageService()->getBlockCount();
      $interval = 0;
      if ($result->hasTimestampParam() && $result->hasPreviousHashParam()) {
        $interval = $this->blockchainService
          ->getStorageService()
          ->getBlocksInterval($result->getTimestampParam(), $result->getPreviousHashParam());
      }

      return BlockchainResponse::create()
        ->setIp($result->getIp())
        ->setPort($result->getPort())
        ->setSecure($result->isSecure())
        ->setStatusCode(200)
        ->setMessageParam('Success')
        ->setCountParam($blockCount)
        ->setIntervalParam($interval)
        ->setDetailsParam('Block count set.')
        ->log($logger)
        ->toJsonResponse();
    }

    return BlockchainResponse::create()
      ->setIp($result->getIp())
      ->setPort($result->getPort())
      ->setSecure($result->isSecure())
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
