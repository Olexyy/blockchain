<?php
/**
 * Created by PhpStorm.
 * User: oos
 * Date: 18.05.18
 * Time: 23:56
 */

namespace Drupal\blockchain\Service;


use Drupal\blockchain\Utils\BlockchainRequestInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

interface BlockchainValidatorServiceInterface {

  /**
   * Validates auth pram by given logic.
   *
   * @param string $self
   *   String self param.
   * @param $auth
   *   String auth param.
   *
   * @return bool
   *   Validation result.
   */
  public function authIsValid($self, $auth);

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
   * @param string $type
   *   Type of operation.
   *
   * @param Request $request
   *   Request object.
   * @return JsonResponse|BlockchainRequestInterface
   *   Execution result.
   */
  public function validateRequest($type, Request $request);

}