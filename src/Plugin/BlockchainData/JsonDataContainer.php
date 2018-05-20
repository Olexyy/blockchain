<?php

namespace Drupal\blockchain\Plugin\BlockchainData;

/**
 * Class SerializableDataContainer.
 *
 * @package Drupal\blockchain\Utils
 */
class JsonDataContainer implements JsonBlockchainDataInterface {

  public $title;

  public $body;

  public static function create(array $values = []) {

    $object  = new static();
    $object->fromArray($values);

    return $object;
  }

  public function fromJson($values) {

    if ($values = json_decode($values)) {
      foreach (get_object_vars($this) as $name => $value) {
        if (isset($values[$name])) {
          $this->{$name} = $values[$name];
        }
      }
    }
  }

  public function fromArray(array $values) {

    if ($values) {
      foreach (get_object_vars($this) as $name => $value) {
        if (isset($values[$name])) {
          $this->{$name} = $values[$name];
        }
      }
    }
  }

  public function toJson() {

    $values = [];
    foreach (get_object_vars($this) as $name => $value) {
      $values[$name] = $value;
    }

    return json_encode($values);
  }

  public function getWidget() {

    $types = [
      'title' => 'textfield',
      'body' => 'textarea',
    ];
    $widget = [];
    foreach (get_object_vars($this) as $name => $value) {
      $type = $types[$name];
      $widget[$name] = [
        '#type' => $type,
        '#default_value' => $value,
        '#title' => t(ucfirst($name)),
      ];
    }

    return $widget;
  }

  public function getFormatter() {

    $view = [];
    foreach (get_object_vars($this) as $name => $value) {
      $view[$name] = [
        '#type' => 'item',
        '#title' => t(ucfirst($name)),
        '#description' => $value,
      ];
    }

    return $view;
  }

  public function toArray() {

    $array = [];
    foreach (get_object_vars($this) as $name => $value) {
      $array[$name] = $value;
    }

    return $array;
  }
}