<?php

namespace Drupal\blockchain\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class BlockchainConfigForm.
 */
class BlockchainConfigForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $blockchain_config = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $blockchain_config->label(),
      '#description' => $this->t("Label for the Blockchain config."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $blockchain_config->id(),
      '#machine_name' => [
        'exists' => '\Drupal\blockchain\Entity\BlockchainConfig::load',
      ],
      '#disabled' => !$blockchain_config->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $blockchain_config = $this->entity;
    $status = $blockchain_config->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Blockchain config.', [
          '%label' => $blockchain_config->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Blockchain config.', [
          '%label' => $blockchain_config->label(),
        ]));
    }
    $form_state->setRedirectUrl($blockchain_config->toUrl('collection'));
  }

}
