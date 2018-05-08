<?php


namespace Drupal\blockchain\Utils;

/**
 * Class Util.
 *
 * @package Drupal\blockchain\Utils
 */
class Util {

  public static function hash($string) {
    return hash('sha256', $string);
  }

}