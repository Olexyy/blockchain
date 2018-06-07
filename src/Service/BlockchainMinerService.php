<?php


namespace Drupal\blockchain\Service;

use Drupal\blockchain\Entity\BlockchainBlockInterface;
use Drupal\blockchain\Utils\Util;

/**
 * Class BlockchainMinerService.
 *
 * @package Drupal\blockchain\Service
 */
class BlockchainMinerService implements BlockchainMinerServiceInterface {

  /**
   * Validator service.
   *
   * @var BlockchainValidatorServiceInterface
   */
  protected $blockchainValidatorService;

  /**
   * BlockchainMinerService constructor.
   *
   * @param BlockchainValidatorServiceInterface $blockchainValidatorService
   *   Injected service.
   */
  public function __construct(BlockchainValidatorServiceInterface $blockchainValidatorService) {

    $this->blockchainValidatorService = $blockchainValidatorService;
  }

  /**
   * {@inheritdoc}
   */
  public function mine($miningString) {

    $nonce = 0;
    $result = Util::hash($miningString.$nonce);
    while (!$this->blockchainValidatorService->hashIsValid($result)) {
      $nonce++;
      $result = Util::hash($miningString.$nonce);
    }

    return $nonce;
  }

  /**
   * {@inheritdoc}
   */
  public function mineBlock(BlockchainBlockInterface $blockchainBlock) {

    $newNonce = $this->mine($blockchainBlock->getMiningString());
    $blockchainBlock->setNonce($newNonce);
  }

}

