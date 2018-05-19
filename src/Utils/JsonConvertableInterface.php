<?php


namespace Drupal\blockchain\Utils;
use Drupal\Core\Form\FormStateInterface;

/**
 * Interface JsonConvertableInterface.
 *
 * @package Drupal\blockchain\Utils
 */
interface JsonConvertableInterface {

  public static function createFromJson($values);

  public static function createFromFormState(FormStateInterface $formState);

  public function toJson();
}