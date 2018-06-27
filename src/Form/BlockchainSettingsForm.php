<?php

namespace Drupal\blockchain\Form;

use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\blockchain\Utils\BlockchainBatchHandler;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BlockchainSettingsForm.
 *
 * @ingroup blockchain
 */
class BlockchainSettingsForm extends FormBase {

  /**
   * Blockchain service.
   *
   * @var BlockchainServiceInterface
   */
  protected $blockchainService;

  /**
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * BlockchainSettingsForm constructor.
   *
   * @param BlockchainServiceInterface $blockchainService
   *   Blockchain service.
   * @param EntityTypeManagerInterface $entityTypeManager
   */
  public function __construct(BlockchainServiceInterface $blockchainService,
                              EntityTypeManagerInterface $entityTypeManager) {

    $this->blockchainService = $blockchainService;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {

    return new static(
      $container->get('blockchain.service'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {

    return 'blockchain_settings';
  }

  /**
   * Defines the settings form for Blockchain Block entities.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   Form definition array.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $this->blockchainService->getConfigService()->setCurrentConfig('blockchain_block');
    $blockCount = $this->blockchainService->getStorageService()->getBlockCount();
    $queueService = $this->blockchainService->getQueueService();
    $countAnnounce = $queueService->getAnnounceQueue()->numberOfItems();
    $countMining = $queueService->getBlockPool()->numberOfItems();
    $form['blockchain_block_wrapper'] = [
      '#type' => 'details',
      '#title' => $this->t('Blockchain block details'),
      '#open' => TRUE,
      '#attributes' => ['class' => ['package-listing']],
    ];
    $form['blockchain_block_wrapper']['block_count'] = [
      '#type' => 'item',
      '#title' => $this->t('Number of blocks in storage'),
      '#markup' => ' - ' . $blockCount . ' - ',
    ];
    $form['blockchain_block_wrapper']['queue_mining_item_count'] = [
      '#type' => 'item',
      '#title' => $this->t('Number of items in block pool'),
      '#markup' => ' - ' . $countMining . ' - ',
    ];
    $form['blockchain_block_wrapper']['do_mining'] = [
      '#type' => 'button',
      '#executes_submit_callback' => TRUE,
      '#submit' => [[$this, 'callbackHandler']],
      '#value' => $this->t('Do mining'),
      '#context' => 'do_mining',
      '#disabled' => !$countMining,
    ];
    $form['blockchain_block_wrapper']['queue_announce_item_count'] = [
      '#type' => 'item',
      '#title' => $this->t('Number of items in announce queue'),
      '#markup' => ' - ' . $countAnnounce . ' - ',
    ];

    return $form;
  }

  /**
   * Callback for custom actions.
   */
  public function callbackHandler(array &$form, FormStateInterface $form_state) {

    $this->getRequest()->query->remove('destination');
    $context = $form_state->getTriggeringElement()['#context'];
    if ($context == 'do_mining') {
      BlockchainBatchHandler::set(BlockchainBatchHandler::getMiningBatchDefinition());
    }
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }
}
