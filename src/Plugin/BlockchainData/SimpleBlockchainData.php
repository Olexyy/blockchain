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

  const KEY = 'simpleBlockchainData';

  /**
   * {@inheritdoc}
   */
  public function setData($data) {

    if (is_string($data)) {
      $this->blockchainBlock->setData($this->dataToSleep($data));
      return TRUE;
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {

    if ($data = $this->blockchainBlock->getData()) {
      return $this->dataWakeUp($this->blockchainBlock->getData());
    }

    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function getSubmitData(FormStateInterface $formState) {

    if ($formState->hasValue(static::KEY)) {
      return $this->dataToSleep($formState->getValue(static::KEY));
    }

    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function getWidget() {

    return [
      static::KEY => [
        '#type' => 'textarea',
        '#required' => TRUE,
        '#title' => $this->t('Data'),
        '#default_value' => $this->getData(),
        '#placeholder' => $this->t('Put block data here'),
      ]
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getView() {

    return [
      '#type' => 'item',
      '#title' => $this->t('Data'),
      '#description' => $this->getData(),
    ];
  }

}
