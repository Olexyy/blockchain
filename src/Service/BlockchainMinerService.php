<?php


namespace Drupal\blockchain\Service;

use Drupal\blockchain\Entity\BlockchainBlockInterface;
use Drupal\blockchain\Utils\MiningTimeoutException;
use Drupal\blockchain\Utils\Util;
use Drupal\Core\Queue\SuspendQueueException;

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
  public function mine($previousHash, $deadline = 0) {

    $nonce = 0;
    $result = Util::hash($previousHash . $nonce);
    while (!$this->blockchainValidatorService->hashIsValid($result)) {
      if ($deadline && $deadline > time()) {
        throw new SuspendQueueException('Block mining timed out');
      }
      $nonce++;
      $result = Util::hash($previousHash . $nonce);
    }

    return $nonce;
  }

  /**
   * {@inheritdoc}
   */
  public function mineBlock(BlockchainBlockInterface $blockchainBlock, $deadline = 0) {

    $nonce = $this->mine($blockchainBlock->getPreviousHash());
    $blockchainBlock->setNonce($nonce);
  }

}

