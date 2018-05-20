<?php


namespace Drupal\blockchain\Utils;

/**
 * Interface JsonConvertableInterface.
 *
 * @package Drupal\blockchain\Utils
 */
interface JsonConvertableInterface {

  public static function createFromJson($values);

  public static function create(array $values = []);

  public function toJson();

  public function getWidgetType($name);
}