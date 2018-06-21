<?php

namespace Drupal\blockchain\Controller;


use Drupal\blockchain\Entity\BlockchainConfigInterface;
use Drupal\blockchain\Service\BlockchainConfigServiceInterface;
use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\blockchain\Utils\BlockchainRequest;
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
   * Blockchain block storage service.
   *
   * @var \Drupal\blockchain\Service\BlockchainStorageServiceInterface
   */
  protected $blockchainBlockStorage;

  /**
   * Request stack.
   *
   * @var RequestStack
   */
  protected $request;

  /**
   * Parsed blockchain request.
   *
   * @var BlockchainRequestInterface
   */
  protected $blockchainRequest;

  /**
   * Validation result.
   *
   * @var BlockchainRequestInterface|BlockchainResponseInterface
   */
  protected $validationResult;

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
    $this->blockchainBlockStorage = $blockchainService->getStorageService();
    $this->request = $requestStack->getCurrentRequest();
    $this->init();
  }

  public function init() {
    $this->blockchainRequest = BlockchainRequest::createFromRequest($this->request);
    $this->blockchainRequest->setRequestType($this->getRequestType());
    $this->validationResult = $this->validate($this->blockchainRequest);
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
    $result = $this->validationResult;
    if ($result instanceof BlockchainResponseInterface) {

      return $result->log($logger)->toJsonResponse();
    }
    elseif ($result instanceof BlockchainRequestInterface) {
      if ($result->hasCountParam()) {
        $ownBlockCount = $this->blockchainBlockStorage->getBlockCount();
        if ($ownBlockCount < $result->getCountParam()) {
          $this->blockchainService->getQueueService()->addAnnounceItem($result->sleep());
          $announceManagement = $this->blockchainService->getConfigService()->getCurrentConfig()->getAnnounceManagement();
          if ($announceManagement == BlockchainConfigInterface::ANNOUNCE_MANAGEMENT_IMMEDIATE) {
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
    $result = $this->validationResult;
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
    $logger->info('Count attempt initiated.');
    $result = $this->validationResult;
    if ($result instanceof BlockchainResponseInterface) {

      return $result->log($logger)->toJsonResponse();
    }
    elseif ($result instanceof BlockchainRequestInterface) {

      return BlockchainResponse::create()
        ->setIp($result->getIp())
        ->setPort($result->getPort())
        ->setSecure($result->isSecure())
        ->setStatusCode(200)
        ->setMessageParam('Success')
        ->setCountParam($this->blockchainBlockStorage->getBlockCount())
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
   * Fetch action.
   *
   * @return JsonResponse
   *   Response by convention.
   */
  public function fetch() {

    $logger = $this->getLogger('blockchain.api');
    $logger->info('Subscribe attempt initiated.');
    $result = $this->validationResult;
    if ($result instanceof BlockchainResponseInterface) {

      return $result->log($logger)->toJsonResponse();
    }
    elseif ($result instanceof BlockchainRequestInterface) {

      if ($result->hasTimestampParam() && $result->hasPreviousHashParam()) {
        if ($block = $this->blockchainBlockStorage
          ->loadByTimestampAndHash($result->getTimestampParam(), $result->getPreviousHashParam())) {
          $exists = TRUE;
          $count = $this->blockchainBlockStorage
            ->getBlocksCountFrom($block);
        }
        else {
          $exists = FALSE;
          $count = 0;
        }

        return BlockchainResponse::create()
          ->setIp($result->getIp())
          ->setPort($result->getPort())
          ->setSecure($result->isSecure())
          ->setStatusCode(200)
          ->setMessageParam('Success')
          ->setExistsParam($exists)
          ->setCountParam($count)
          ->setDetailsParam('Block '. $exists? 'exists' : 'not exists' .'.')
          ->log($logger)
          ->toJsonResponse();
      }
      else {

        return BlockchainResponse::create()
          ->setIp($result->getIp())
          ->setPort($result->getPort())
          ->setSecure($result->isSecure())
          ->setStatusCode(200)
          ->setExistsParam(FALSE)
          ->setCountParam($this->blockchainBlockStorage->getBlockCount())
          ->setMessageParam('Downgraded to count response')
          ->setDetailsParam('No timestamp or/and previous hash param.')
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
   * Pull action.
   *
   * @return JsonResponse
   *   Response by convention.
   */
  public function pull() {

    $logger = $this->getLogger('blockchain.api');
    $logger->info('Pull attempt initiated.');
    $result = $this->validationResult;
    if ($result instanceof BlockchainResponseInterface) {

      return $result->log($logger)->toJsonResponse();
    }
    elseif ($result instanceof BlockchainRequestInterface) {

      if ($result->hasCountParam()) {
        if ($result->hasTimestampParam() && $result->hasPreviousHashParam()) {
          if ($block = $this->blockchainBlockStorage
            ->loadByTimestampAndHash($result->getTimestampParam(), $result->getPreviousHashParam())) {
            $exists = TRUE;
            $blocks = $this->blockchainBlockStorage
              ->getBlocksFrom($block, $result->getCountParam());
          } else {
            $exists = FALSE;
            $blocks = [];
          }

          return BlockchainResponse::create()
            ->setIp($result->getIp())
            ->setPort($result->getPort())
            ->setSecure($result->isSecure())
            ->setStatusCode(200)
            ->setMessageParam('Success')
            ->setExistsParam($exists)
            ->setBlocksParam($blocks)
            ->setDetailsParam('Block ' . $exists ? 'exists' : 'not exists' . '.')
            ->log($logger)
            ->toJsonResponse();
        }
        else {
          $blocks = $this->blockchainBlockStorage
            ->getBlocks(0, $result->getCountParam(), TRUE);

          return BlockchainResponse::create()
            ->setIp($result->getIp())
            ->setPort($result->getPort())
            ->setSecure($result->isSecure())
            ->setStatusCode(200)
            ->setMessageParam('Success')
            ->setExistsParam(FALSE)
            ->setBlocksParam($blocks)
            ->setDetailsParam('Returning results starting form generic.')
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
          ->setDetailsParam('No count or/and timestamp or/and previous hash param.')
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
   * Request validator.
   *
   * This validates request according to defined protocol
   * and returns JsonResponse in case of fail or BlockchainRequest
   * in case if request is valid.
   *
   * @param BlockchainRequestInterface $blockchainRequest
   *   Blockchain request.
   *
   * @return BlockchainResponseInterface|BlockchainRequestInterface
   *   Execution result.
   */
  public function validate(BlockchainRequestInterface $blockchainRequest) {

    return $this->blockchainService
      ->getValidatorService()
      ->validateRequest($blockchainRequest, $this->request);
  }

  /**
   * Getter for request type.
   *
   * @return null|string
   *   Name of request method.
   */
  public function getRequestType() {

    if ($route = $this->request->attributes->get('_route')) {
      if ($parts = explode('.', $route)) {
        if (count($parts) === 2 &&$parts[0] == 'blockchain') {

          return $parts[1];
        }
      }
    }

    return NULL;
  }

}
