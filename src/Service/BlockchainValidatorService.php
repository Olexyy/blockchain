<?php

namespace Drupal\blockchain\Service;

use Drupal\blockchain\Utils\BlockchainRequest;
use Drupal\blockchain\Utils\BlockchainRequestInterface;
use Drupal\blockchain\Utils\BlockchainResponse;
use Drupal\blockchain\Utils\Util;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BlockchainValidatorService.
 *
 * @package Drupal\blockchain\Service
 */
class BlockchainValidatorService implements BlockchainValidatorServiceInterface {

  /**
   * Config service.
   *
   * @var BlockchainConfigServiceInterface
   */
  protected $configService;

  /**
   * Blockchain Node service.
   *
   * @var BlockchainNodeServiceInterface
   */
  protected $blockchainNodeService;

  /**
   * {@inheritdoc}
   */
  public function __construct(BlockchainConfigServiceInterface $blockchainSettingsService,
                              BlockchainNodeServiceInterface $blockchainNodeService) {

    $this->configService = $blockchainSettingsService;
    $this->blockchainNodeService = $blockchainNodeService;
  }

  /**
   * {@inheritdoc}
   */
  public function hashIsValid($hash) {

    $powPosition = $this->configService->getPowPosition();
    $powExpression = $this->configService->getPowExpression();
    $length = strlen($powExpression);
    if ($powPosition === BlockchainConfigServiceInterface::POW_POSITION_START) {
      if (substr($hash, 0, $length) === $powExpression) {
        return TRUE;
      }
    }
    else {
      if (substr($hash, -$length) === $powExpression) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function authIsValid($self, $auth) {

    $blockchainId = $this->configService->getBlockchainId();

    return Util::hash($blockchainId.$self) === $auth;
  }

  /**
   * {@inheritdoc}
   */
  public function validateRequest($type, Request $request) {

    $configService = $this->configService;
    if ($request->getMethod() !== Request::METHOD_POST) {

      return BlockchainResponse::create()
        ->setIp($request->getClientIp())
        ->setStatusCode(400)
        ->setMessageParam('Bad request')
        ->setDetailsParam('Incorrect method.');
    }
    if ($configService->getBlockchainType() === BlockchainConfigServiceInterface::TYPE_SINGLE) {

      return BlockchainResponse::create()
        ->setIp($request->getClientIp())
        ->setStatusCode(403)
        ->setMessageParam('Forbidden')
        ->setDetailsParam('Access to this resource is restricted.');
    }
    $request = BlockchainRequest::createFromRequest($request, $type);
    if (!$request->hasSelfParam()) {

      return BlockchainResponse::create()
        ->setIp($request->getIp())
        ->setStatusCode(400)
        ->setMessageParam('Bad request')
        ->setDetailsParam('No self param.');
    }
    if ($configService->isBlockchainAuth()) {
      if (!$authToken = $request->getAuthParam()) {

        return BlockchainResponse::create()
          ->setIp($request->getIp())
          ->setStatusCode(401)
          ->setMessageParam('Unauthorized')
          ->setDetailsParam('Auth token required.');
      }
      if (!$this->authIsValid($request->getSelfParam(), $request->getAuthParam())) {

        return BlockchainResponse::create()
          ->setIp($request->getIp())
          ->setStatusCode(401)
          ->setMessageParam('Unauthorized')
          ->setDetailsParam('Auth token invalid.');
      }
    }
    if ($request->getType() !== BlockchainRequestInterface::TYPE_SUBSCRIBE) {
      if (!$blockchainNode = $this->blockchainNodeService->load($request->getSelfParam())) {

        return BlockchainResponse::create()
          ->setIp($request->getIp())
          ->setStatusCode(401)
          ->setMessageParam('Unauthorized')
          ->setDetailsParam('Not subscribed yet.');
      }
    }
    if ($filterList = $configService->getBlockchainFilterListAsArray()) {
      if ($configService->getBlockchainFilterType() === BlockchainConfigServiceInterface::FILTER_TYPE_BLACKLIST) {
        if (in_array($request->getIp(), $filterList)) {

          return BlockchainResponse::create()
            ->setIp($request->getIp())
            ->setStatusCode(403)
            ->setMessageParam('Forbidden')
            ->setDetailsParam('You are forbidden to access this resource.');
        }
      }
      else {
        if (!in_array($request->getIp(), $filterList)) {

          return BlockchainResponse::create()
            ->setIp($request->getIp())
            ->setStatusCode(403)
            ->setMessageParam('Forbidden')
            ->setDetailsParam('You are forbidden to access this resource.');
        }
      }
    }

    return $request;
  }

}
