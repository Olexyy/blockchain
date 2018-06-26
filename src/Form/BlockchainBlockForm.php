<?php

namespace Drupal\blockchain\Form;


use Drupal\blockchain\Service\BlockchainService;
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

    $entity = $this->entity;
    if (!$this->blockchainService->getConfigService()->exists($entity->getEntityTypeId())) {
      $this->blockchainService->getConfigService()->discoverBlockchainConfigs();
    }
    $this->blockchainService->getConfigService()->setCurrentConfig($entity->getEntityTypeId());
    if (!$this->blockchainService->getStorageService()->anyBlock()) {
      $form['message'] = [
        '#type' => 'item',
        '#title' => $this->t('There are no blocks in list yet'),
        '#description' => $this->t('Click below to generate first generic block.'),
      ];
      $form['action']['put_generic_block'] = [
        '#type' => 'button',
        '#executes_submit_callback' => TRUE,
        '#value' => $this->t('Put generic block'),
        '#context' => 'put_generic_block',
        '#submit' =>  [ [$this, 'callbackHandler'] ],
      ];
      $this->init($form_state);
    }
    else {
      $form = parent::buildForm($form, $form_state);
    }

    return $form;
  }

  /**
   * Callback for custom actions.
   */
  public function callbackHandler(array &$form, FormStateInterface $form_state) {

    $this->getRequest()->query->remove('destination');
    $genericBlock = $this->blockchainService->getStorageService()->getGenericBlock();
    $this->blockchainService->getStorageService()->save($genericBlock);
    $this->messenger()->addStatus($this->t('Generic block created'));
    $form_state->setRedirect('<current>');
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {

    /* @var $entity \Drupal\blockchain\Entity\BlockchainBlockInterface */
    $entity = $this->entity;
    // Here we pass raw data.
    $this->blockchainService->getQueueService()->addBlockItem($entity->getData(), $entity->getEntityTypeId());
    // Set to queue (pool) and process it conditionally.
    //$status = parent::save($form, $form_state);
    $batch = [
      'title' => $this->t('Mining block...'),
      'operations' => [
        [ static::class . '::processBatch', [] ],
      ],
      'finished' => static::class .'::finalizeBatch',
    ];
    batch_set($batch);
    /*
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
    */
  }

  /**
   * Batch processor.
   *
   * @param $context
   *   Batch context.
   */
  public static function processBatch(&$context) {

    $blockchainService = BlockchainService::instance();
    $message = t('Mining is in progress...');
    $results = [];
    $results[] = $blockchainService->getQueueService()->doMining();
    $context['message'] = $message;
    $context['results'] = $results;
  }

  /**
   * Batch finalizer.
   *
   * {@inheritdoc}
   */
  public static function finalizeBatch($success, $results, $operations) {

    if ($success) {
      $message = t('@count blocks processed.', [
        '@count' => count($results),
      ]);
    }
    else {
      $message = t('Finished with an error.');
    }
    \Drupal::messenger()->addStatus($message);
  }

}
