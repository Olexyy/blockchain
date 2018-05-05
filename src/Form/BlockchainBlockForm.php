<?php

namespace Drupal\blockchain\Form;

use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for Blockchain Block edit forms.
 *
 * @ingroup blockchain
 */
class BlockchainBlockForm extends ContentEntityForm {

  /**
   * Blockchain service.
   *
   * @var BlockchainServiceInterface
   */
  protected $blockchainService;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    EntityManagerInterface $entity_manager,
    EntityTypeBundleInfoInterface $entity_type_bundle_info = NULL,
    TimeInterface $time = NULL,
    BlockchainServiceInterface $blockchainService) {

    parent::__construct($entity_manager, $entity_type_bundle_info, $time);
    $this->blockchainService = $blockchainService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {

    return new static(
      $container->get('entity.manager'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time'),
      $container->get('blockchain.service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);
    $this->prepareForm($form);

    return $form;
  }

  /**
   * Prepares form depending on blockchain data handler.
   *
   * @param array $form
   *   Render array.
   */
  protected function prepareForm(array &$form) {

    /* @var $entity \Drupal\blockchain\Entity\BlockchainBlockInterface */
    $entity = $this->entity;
    unset($form['data']);
    $dataHandler = $this->blockchainService->getBlockchainDataHandler($entity);
    $form = array_merge($form, $dataHandler->getWidget());
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {

    /* @var $entity \Drupal\blockchain\Entity\BlockchainBlockInterface */
    $entity = $this->entity;
    $dataHandler = $this->blockchainService->getBlockchainDataHandler($entity);
    $dataHandler->setSubmitData($form_state);
    // Set to queue and process it conditionally.
    //$status = parent::save($form, $form_state);
    $status = NULL;
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
