<?php

namespace Drupal\blockchain\Service;


use Drupal\blockchain\Entity\BlockchainBlockInterface;
use Drupal\blockchain\Entity\BlockchainConfigInterface;
use Drupal\blockchain\Plugin\BlockchainAuthManager;
use Drupal\blockchain\Utils\BlockchainRequestInterface;
use Drupal\blockchain\Utils\BlockchainResponse;
use Drupal\blockchain\Utils\Util;
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
   * Auth manager.
   *
   * @var BlockchainAuthManager
   */
  protected $blockchainAuthManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(BlockchainConfigServiceInterface $blockchainSettingsService,
                              BlockchainNodeServiceInterface $blockchainNodeService,
                              BlockchainAuthManager $blockchainAuthManager) {

    $this->configService = $blockchainSettingsService;
    $this->blockchainNodeService = $blockchainNodeService;
    $this->blockchainAuthManager = $blockchainAuthManager;
  }

  /**
   * {@inheritdoc}
   */
  public function hashIsValid($hash) {

    $powPosition = $this->configService->getCurrentConfig()->getPowPosition();
    $powExpression = $this->configService->getCurrentConfig()->getPowExpression();
    $length = strlen($powExpression);
    if ($powPosition === BlockchainConfigInterface::POW_POSITION_START) {
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
  public function blockIsValid(BlockchainBlockInterface $blockchainBlock, BlockchainBlockInterface $previousBlock = NULL) {

    $hashString = Util::hash($blockchainBlock->getPreviousHash() . $blockchainBlock->getNonce());
    if (!$previousBlock) {

      return $this->hashIsValid($hashString);
    }

    return $previousBlock->getHash() == $blockchainBlock->getPreviousHash() &&
      $this->hashIsValid($hashString);
  }

  public function validateRequestContext($type) {

  }

  /**
   * {@inheritdoc}
   */
  public function validateRequest(BlockchainRequestInterface $blockchainRequest, Request $request) {

    $configService = $this->configService;
    if ($request->getMethod() !== Request::METHOD_POST) {

      return BlockchainResponse::create()
        ->setIp($request->getClientIp())
        ->setPort($request->getPort())
        ->setSecure($request->isSecure())
        ->setStatusCode(400)
        ->setMessageParam('Bad request')
        ->setDetailsParam('Incorrect method.');
    }
    if (!$blockchainRequest->hasTypeParam()) {

      return BlockchainResponse::create()
        ->setIp($request->getClientIp())
        ->setPort($request->getPort())
        ->setSecure($request->isSecure())
        ->setStatusCode(400)
        ->setMessageParam('Bad request')
        ->setDetailsParam('Missing type param.');
    }
    if (!$this->configService->exists($blockchainRequest->getTypeParam())) {

      return BlockchainResponse::create()
        ->setIp($request->getClientIp())
        ->setPort($request->getPort())
        ->setSecure($request->isSecure())
        ->setStatusCode(400)
        ->setMessageParam('Bad request')
        ->setDetailsParam('Invalid type param.');
    }

    // Set blockchain config as it is validated.
    // TODO DO IT NOT HERE
    $this->configService
      ->setCurrentConfig($blockchainRequest->getTypeParam());

    if (!$request->isSecure() && !$this->configService->getAllowNotSecure()) {

      return BlockchainResponse::create()
        ->setIp($request->getClientIp())
        ->setPort($request->getPort())
        ->setSecure($request->isSecure())
        ->setStatusCode(400)
        ->setMessageParam('Bad request')
        ->setDetailsParam('Incorrect protocol.');
    }
    if ($configService->getCurrentConfig()->getType() === BlockchainConfigInterface::TYPE_SINGLE) {

      return BlockchainResponse::create()
        ->setIp($request->getClientIp())
        ->setPort($request->getPort())
        ->setSecure($request->isSecure())
        ->setStatusCode(403)
        ->setMessageParam('Forbidden')
        ->setDetailsParam('Access to this resource is restricted.');
    }
    if (!$blockchainRequest->hasSelfParam()) {

      return BlockchainResponse::create()
        ->setIp($blockchainRequest->getIp())
        ->setPort($request->getPort())
        ->setSecure($request->isSecure())
        ->setStatusCode(400)
        ->setMessageParam('Bad request')
        ->setDetailsParam('No self param.');
    }
    if ($authHandler = $this->blockchainAuthManager->getHandler($configService->getCurrentConfig())) {
      if(!$authHandler->authorize($blockchainRequest)) {
        return BlockchainResponse::create()
          ->setIp($blockchainRequest->getIp())
          ->setPort($request->getPort())
          ->setSecure($request->isSecure())
          ->setStatusCode(401)
          ->setMessageParam('Unauthorized')
          ->setDetailsParam('Auth token invalid.');
      }
    }
    if ($blockchainRequest->getRequestType() !== BlockchainRequestInterface::TYPE_SUBSCRIBE) {
      if (!$blockchainNode = $this->blockchainNodeService->load($blockchainRequest->getSelfParam())) {

        return BlockchainResponse::create()
          ->setIp($blockchainRequest->getIp())
          ->setPort($request->getPort())
          ->setSecure($request->isSecure())
          ->setStatusCode(401)
          ->setMessageParam('Unauthorized')
          ->setDetailsParam('Not subscribed yet.');
      }
    }
    if ($filterList = $configService->getBlockchainFilterListAsArray()) {
      if ($configService->getCurrentConfig()->getFilterType() === BlockchainConfigInterface::FILTER_TYPE_BLACKLIST) {
        if (in_array($blockchainRequest->getIp(), $filterList)) {

          return BlockchainResponse::create()
            ->setIp($blockchainRequest->getIp())
            ->setPort($request->getPort())
            ->setSecure($request->isSecure())
            ->setStatusCode(403)
            ->setMessageParam('Forbidden')
            ->setDetailsParam('You are forbidden to access this resource.');
        }
      }
      else {
        if (!in_array($blockchainRequest->getIp(), $filterList)) {

          return BlockchainResponse::create()
            ->setIp($blockchainRequest->getIp())
            ->setPort($request->getPort())
            ->setSecure($request->isSecure())
            ->setStatusCode(403)
            ->setMessageParam('Forbidden')
            ->setDetailsParam('You are forbidden to access this resource.');
        }
      }
    }

    return $blockchainRequest;
  }

  /**
   * {@inheritdoc}
   */
  public function validateBlocks(array $blocks) {

    $previousBlock = NULL;
    foreach ($blocks as $block) {
      if (!$this->blockIsValid($block, $previousBlock)) {

        return FALSE;
      }
      $previousBlock = $block;
    }

    return TRUE;
  }

}
