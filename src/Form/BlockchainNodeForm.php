<?php

namespace Drupal\blockchain\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class BlockchainNodeForm.
 */
class BlockchainNodeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $blockchain_node = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $blockchain_node->label(),
      '#description' => $this->t("Label for the Blockchain Node."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $blockchain_node->id(),
      '#machine_name' => [
        'exists' => '\Drupal\blockchain\Entity\BlockchainNode::load',
      ],
      '#disabled' => !$blockchain_node->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $blockchain_node = $this->entity;
    $status = $blockchain_node->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Blockchain Node.', [
          '%label' => $blockchain_node->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Blockchain Node.', [
          '%label' => $blockchain_node->label(),
        ]));
    }
    $form_state->setRedirectUrl($blockchain_node->toUrl('collection'));
  }

}
