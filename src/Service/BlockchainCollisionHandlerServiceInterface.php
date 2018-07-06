<?php


namespace Drupal\blockchain\Service;

use Drupal\blockchain\Utils\BlockchainResponseInterface;

/**
 * Interface BlockchainCollisionHandlerServiceInterface.
 *
 * @package Drupal\blockchain\Service
 */
interface BlockchainCollisionHandlerServiceInterface {

  /**
   * Validator function.
   *
   * Grants pull based on response.
   *
   * @param BlockchainResponseInterface $response
   *   Blockchain response.
   *
   * @return bool
   *   Test result.
   */
  public function isPullGranted(BlockchainResponseInterface $response);

  /**
   * Attempts to manage PULL by given endpoint address.
   *
   * @param BlockchainResponseInterface $fetchResponse
   *   Fetch response.
   * @param $endPoint
   *   Given endpoint.
   *
   * @throws \Exception
   */
  public function pullNoConflict(BlockchainResponseInterface $fetchResponse, $endPoint);

  /**
   * Attempts to manage PULL by given endpoint address.
   *
   * @param BlockchainResponseInterface $fetchResponse
   *   Fetch response.
   * @param $endPoint
   *   Given endpoint.
   *
   * @throws \Exception
   */
  public function pullWithConflict(BlockchainResponseInterface $fetchResponse, $endPoint);

}
