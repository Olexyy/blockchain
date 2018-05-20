<?php

namespace Drupal\blockchain\Utils;

/**
 * Class SerializableDataContainer.
 *
 * @package Drupal\blockchain\Utils
 */
class JsonDataContainer implements JsonConvertableInterface {

  public $title;
  public $body;

  public function __construct(array $values = []) {
    foreach (get_object_vars($this) as $name => $value) {
      if (isset($values[$name])) {
        $this->{$name} = $values[$name];
      }
    }
  }

  public static function create(array $values = []) {
    return new static($values);
  }

  public static function createFromJson($values) {

    if ($values = json_decode($values)) {
      return new static($values);
    }

    return new static();
  }

  public function toJson() {

    $values = [];
    foreach (get_object_vars($this) as $name => $value) {
      $values[$name] = $value;
    }

    return json_encode($values);
  }

  public function getWidgetType($name) {

    $types = [
      'title' => 'textfield',
      'body' => 'textarea',
    ];
    if (isset($types[$name])) {
      return $types[$name];
    }

    return NULL;
  }
}