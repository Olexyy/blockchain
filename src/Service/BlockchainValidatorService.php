<?php

namespace Drupal\blockchain\Service;

use Drupal\blockchain\Utils\BlockchainRequest;
use Drupal\blockchain\Utils\BlockchainRequestInterface;
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
  public function tokenGenerate() {

    return Util::hash($this->configService->getBlockchainId().$this->configService->getBlockchainNodeId());
  }

  /**
   * {@inheritdoc}
   */
  public function validateRequest($type, Request $request) {

    $configService = $this->configService;
    if ($configService->getBlockchainType() === BlockchainConfigServiceInterface::TYPE_SINGLE) {
      return JsonResponse::create([
        'message' => 'Forbidden',
        'details' => 'Access to this resource is restricted.'
      ], 403);
    }
    $request = BlockchainRequest::createFromRequest($request, $type);
    if (!$request->hasSelfParam()) {
      return JsonResponse::create([
        'message' => 'Bad request',
        'details' => 'No self param.'
      ], 400);
    }
    if ($configService->isBlockchainAuth()) {
      if (!$authToken = $request->getAuthParam()) {
        return JsonResponse::create([
          'message' => 'Unauthorized',
          'details' => 'Auth token required.'
        ], 401);
      }
      if (!$this->authIsValid(
        $request->getSelfParam(), $request->getAuthParam())) {
        return JsonResponse::create([
          'message' => 'Unauthorized',
          'details' => 'Auth token is invalid.'
        ], 401);
      }
    }
    if ($request->getType() !== BlockchainRequestInterface::TYPE_SUBSCRIBE) {
      if (!$blockchainNode = $this->blockchainNodeService->load($request->getSelfParam())) {
        return JsonResponse::create([
          'message' => 'Unauthorized',
          'details' => 'Not subscribed yet.'
        ], 401);
      }
    }
    if ($filterList = $configService->getBlockchainFilterListAsArray()) {
      if ($configService->getBlockchainFilterType() === BlockchainConfigServiceInterface::FILTER_TYPE_BLACKLIST) {
        if (in_array($request->getIp(), $filterList)) {
          return JsonResponse::create([
            'message' => 'Forbidden',
            'details' => 'You are forbidden to access this resource.'
          ], 403);
        }
      }
      else {
        if (!in_array($request->getIp(), $filterList)) {
          return JsonResponse::create([
            'message' => 'Forbidden',
            'details' => 'You are forbidden to access this resource.'
          ], 403);
        }
      }
    }

    switch ($request->getType()) {
      case BlockchainRequestInterface::TYPE_SUBSCRIBE:

        break;
    }

    return $request;
  }

}
