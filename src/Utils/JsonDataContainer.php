<?php

namespace Drupal\blockchain\Utils;
use Drupal\Core\Form\FormStateInterface;

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
        $this->{$name} = $value;
      }
    }
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

  public static function createFromFormState(FormStateInterface $formState){

    $object = new static();
    foreach (get_object_vars($object) as $name => $value) {
      if ($formState->hasValue($name)) {
        $object->{$name} = $formState->getValue($name);
      }
    }
    return $object;
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