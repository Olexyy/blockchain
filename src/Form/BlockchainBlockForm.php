<?php

namespace Drupal\blockchain\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Blockchain Block edit forms.
 *
 * @ingroup blockchain
 */
class BlockchainBlockForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\blockchain\Entity\BlockchainBlock */
    $form = parent::buildForm($form, $form_state);

    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Blockchain Block.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Blockchain Block.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.blockchain_block.canonical', ['blockchain_block' => $entity->id()]);
  }

}
