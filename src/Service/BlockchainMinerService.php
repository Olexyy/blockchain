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
  public function mine($previousHash) {

    $nonce = 0;
    $result = Util::hash($previousHash . $nonce);
    while (!$this->blockchainValidatorService->hashIsValid($result)) {
      $nonce++;
      $result = Util::hash($previousHash . $nonce);
    }

    return $nonce;
  }

  /**
   * {@inheritdoc}
   */
  public function mineBlock(BlockchainBlockInterface $blockchainBlock) {

    $nonce = $this->mine($blockchainBlock->getPreviousHash());
    $blockchainBlock->setNonce($nonce);
  }

}

