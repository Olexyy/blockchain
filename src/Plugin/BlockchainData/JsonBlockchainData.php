<?php

namespace Drupal\blockchain\Plugin\BlockchainData;

use Drupal\blockchain\Plugin\BlockchainDataBase;
use Drupal\blockchain\Utils\JsonConvertableInterface;
use Drupal\blockchain\Utils\JsonDataContainer;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * NodeBlockchainData based on json serializable class.
 *
 * @BlockchainData(
 *  id = "json",
 *  label = @Translation("Json data"),
 *  settings = true
 * )
 */
class JsonBlockchainData extends BlockchainDataBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function setData($data) {

    if ($data instanceof JsonConvertableInterface) {
      $data = $data->toJson();
      $this->data = $this->dataToSleep($data);
      return TRUE;
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {

    if ($this->data) {
      $data = $this->dataWakeUp($this->data);

      return JsonDataContainer::createFromJson($data);
    }

    return new JsonDataContainer();
  }

  /**
   * {@inheritdoc}
   */
  public function getRawData() {

    if ($this->data) {
      return $this->data;
    }

    return '';
  }

  /**
   *
   *
   * {@inheritdoc}
   */
  public function getWidget() {

    $widget = [];

    foreach (get_object_vars($this->getData()) as $name => $value) {

      $type = $this->getData()->getWidgetType($name)?
        $this->getData()->getWidgetType($name) : 'textfield';

      $widget[$name] = [
        '#type' => $type,
        '#default_value' => $value,
        '#title' => $this->t(ucfirst($name)),
      ];
    }

    return $widget;
  }

  /**
   *
   *
   * {@inheritdoc}
   */
  public function getFormatter() {

    $view = [];

    foreach (get_object_vars($this->getData()) as $name => $value) {
      $view[$name] = [
        '#type' => 'item',
        '#title' => $this->t(ucfirst($name)),
        '#description' => $value,
      ];
    }

    return $view;
  }

  /**
   *
   *
   * {@inheritdoc}
   */
  public function extractFormValues(FieldItemListInterface $items, array $form, FormStateInterface $form_state) {

    $entity = JsonDataContainer::createFromFormState($form_state);

    foreach ($items as $key => $item) {
      $this->setData($entity->toJson());
      $item->value = $this->getRawData();
    }

  }


}
