<?php

namespace Drupal\blockchain\Utils;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class BlockchainResponse
 *
 * @package Drupal\blockchain\Utils
 */
class BlockchainResponse extends BlockchainHttpBase implements BlockchainResponseInterface {

  /**
   * Status code.
   *
   * @var string
   */
  protected $statusCode;

  /**
   * {@inheritdoc}
   */
  public function getStatusCode() {

    return $this->statusCode;
  }

  /**
   * {@inheritdoc}
   */
  public function setStatusCode($statusCode) {

    $this->statusCode = $statusCode;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getMessageParam() {

    return $this->getParam(static::PARAM_MESSAGE);
  }

  /**
   * {@inheritdoc}
   */
  public function setMessageParam($message) {

    return $this->setParam(static::PARAM_MESSAGE, $message);
  }

  /**
   * {@inheritdoc}
   */
  public function getDetailsParam() {

    return $this->getParam(static::PARAM_DETAILS);
  }

  /**
   * {@inheritdoc}
   */
  public function setDetails($details) {

    return $this->setParam(static::PARAM_DETAILS, $details);
  }

  /**
   * {@inheritdoc}
   */
  public function toJsonResponse() {

    return new JsonResponse(
      $this->params, $this->statusCode
    );
  }

  /**
   * {@inheritdoc}
   */
  public function isStatusOk() {

    return $this->statusCode == 200;
  }

  /**
   * {@inheritdoc}
   */
  public static function create() {

    return new static();
  }

}