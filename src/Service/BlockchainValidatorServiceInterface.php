<?php

namespace Drupal\blockchain\Service;


use Drupal\blockchain\Entity\BlockchainBlockInterface;
use Drupal\blockchain\Utils\BlockchainRequest;
use Drupal\blockchain\Utils\BlockchainRequestInterface;
use Drupal\blockchain\Utils\BlockchainResponseInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface BlockchainValidatorServiceInterface.
 *
 * @package Drupal\blockchain\Service
 */
interface BlockchainValidatorServiceInterface {

  /**
   * Validates hash according to given Pow rules.
   *
   * @param string $hash
   *   Hash.
   *
   * @return bool
   *   Test result.
   */
  public function hashIsValid($hash);

  /**
   * Request validator.
   *
   * This validates request according to defined protocol
   * and returns JsonResponse in case of fail or BlockchainRequest
   * in case if request is valid.
   *
   * @param BlockchainRequestInterface $blockchainRequest
   *   Request object.
   * @param Request $request
   *   Request object.
   *
   * @return BlockchainResponseInterface|BlockchainRequestInterface
   *   Execution result.
   */
  public function validateRequest(BlockchainRequestInterface $blockchainRequest, Request $request);

  /**
   * Validates block against validation rules and previous block if passed.
   *
   * @param BlockchainBlockInterface $blockchainBlock
   *   Blockchain block.
   * @param BlockchainBlockInterface|null $previousBlock
   *   Previous block.
   * @return bool
   *
   *   Test result.
   */
  public function blockIsValid(BlockchainBlockInterface $blockchainBlock, BlockchainBlockInterface $previousBlock = NULL);

  /**
   * Validates blocks in given array.
   *
   * Note that first block in array is validated only by nonce.
   * To validate blocks in existing database use BlockchainStorage
   * method checkBlocks().
   *
   * @param array|BlockchainBlockInterface[] $blocks
   *   Given blocks.
   *
   * @return bool
   *   Execution result.
   */
  public function validateBlocks(array $blocks);

}
