<?php

namespace Drupal\blockchain\Plugin\BlockchainData;

use Drupal\blockchain\Plugin\BlockchainDataBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * BlockchainBlockData as simple string.
 *
 * @BlockchainData(
 *  id = "simple",
 *  label = @Translation("Simple string data")
 * )
 */
class SimpleBlockchainData extends BlockchainDataBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function setData($data) {

    if (is_string($data)) {
      $this->data = $this->dataToSleep($data);
      return TRUE;
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function setSubmitData(FormStateInterface $formState) {

    /*if ($formState->hasValue(static::KEY)) {
      $this->data = $this->dataToSleep($formState->getValue(static::KEY));
    }*/

  }

  /**
   * {@inheritdoc}
   */
  public function getData() {

    if ($this->data) {
      return $this->dataWakeUp($this->data);
    }

    return '';
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
   * {@inheritdoc}
   */
  public function getWidget() {

    return [
      '#type' => 'textarea',
      '#required' => TRUE,
      '#title' => $this->t('Data'),
      '#default_value' => $this->getData(),
      '#disabled' => (bool) $this->getData(),
      '#description' => $this->t('Block data'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormatter() {

    return [
      '#type' => 'item',
      '#title' => $this->t('Data'),
      '#description' => $this->getData(),
    ];
  }

}
