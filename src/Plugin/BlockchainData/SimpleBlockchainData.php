<?php

namespace Drupal\blockchain\Plugin\BlockchainData;

use Drupal\blockchain\Plugin\BlockchainDataBase;
use Drupal\blockchain\Utils\Util;
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
      $this->blockchainBlock->setData($data);
      return TRUE;
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {

    return $this->blockchainBlock->getData();
  }

  public function getWidget() {

    return [
      static::KEY => [
        '#type' => 'textarea',
        '#required' => TRUE,
        '#title' => $this->t('Data'),
      ]
    ];
  }

  public function setSubmitData(FormStateInterface $formState) {

    if ($formState->hasValue(static::KEY)) {
      $this->setData($formState->getValue(static::KEY));
    }

  }

  public function getView($data) {

    return [
      '#type' => 'item',
      '#title' => $this->t('Data'),
      '#description' => $data,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getHash() {

    return Util::hash(
    $this->blockchainBlock->getData().
    $this->blockchainBlock->getTimestamp().
    $this->blockchainBlock->getNonce());
  }

}