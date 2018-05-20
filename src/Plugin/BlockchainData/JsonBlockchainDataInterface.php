<?php


namespace Drupal\blockchain\Plugin\BlockchainData;

/**
 * Interface JsonConvertableInterface.
 *
 * @package Drupal\blockchain\Utils
 */
interface JsonBlockchainDataInterface {

  public static function create(array $values = []);

  public function toJson();

  public function toArray();

  public function fromJson($values);

  public function fromArray(array $values);

  public function getWidget();

  public function getFormatter();

}