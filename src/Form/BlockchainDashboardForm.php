<?php

namespace Drupal\blockchain\Form;

use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\blockchain\Utils\BlockchainBatchHandler;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BlockchainDashboardForm.
 *
 * @ingroup blockchain
 */
class BlockchainDashboardForm extends FormBase {

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
   * BlockchainDashboardForm constructor.
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

    return 'blockchain_dashboard';
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

    $blockchainConfigs = $this->blockchainService->getConfigService()->getAll();
    if (!$blockchainConfigs) {
      $form['empty'] = [
        '#type' => 'item',
        '#title' => $this->t('No blockchain configs created'),
        '#markup' => $this->t('Go to related tab and create config based on blockchain type.'),
      ];
    }
    else {
      foreach ($blockchainConfigs as $blockchainConfig) {
        $blockchainConfigId = $blockchainConfig->id();
        $this->blockchainService->getConfigService()->setCurrentConfig($blockchainConfigId);
        $blockCount = $this->blockchainService->getStorageService()->getBlockCount();
        $queueService = $this->blockchainService->getQueueService();
        $countAnnounce = $queueService->getAnnounceQueue()->numberOfItems();
        $countMining = $queueService->getBlockPool()->numberOfItems();
        $form[$blockchainConfigId . '_wrapper'] = [
          '#type' => 'details',
          '#title' => $this->t('Blockchain block details'),
          '#open' => TRUE,
          '#attributes' => ['class' => ['package-listing']],
        ];
        $form[$blockchainConfigId . '_wrapper']['block_count'] = [
          '#type' => 'item',
          '#title' => $this->t('Number of blocks in storage'),
          '#markup' => $blockCount,
        ];
        $form[$blockchainConfigId . '_wrapper']['queue_mining_item_count'] = [
          '#type' => 'item',
          '#title' => $this->t('Number of items in block pool'),
          '#markup' => $countMining,
        ];
        $form[$blockchainConfigId . '_wrapper']['do_mining'] = [
          '#type' => 'button',
          '#executes_submit_callback' => TRUE,
          '#submit' => [[$this, 'callbackHandler']],
          '#value' => $this->t('Do mining'),
          '#context' => 'do_mining',
          '#disabled' => !$countMining,
        ];
        $form[$blockchainConfigId . '_wrapper']['queue_announce_item_count'] = [
          '#type' => 'item',
          '#title' => $this->t('Number of items in announce queue'),
          '#markup' => $countAnnounce,
        ];
        $form[$blockchainConfigId . '_wrapper']['process_announce'] = [
          '#type' => 'button',
          '#executes_submit_callback' => TRUE,
          '#submit' => [[$this, 'callbackHandler']],
          '#value' => $this->t('Process announces'),
          '#context' => 'process_announce',
          '#disabled' => !$countAnnounce,
        ];
      }
    }

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
    elseif ($context == 'process_announce') {
      BlockchainBatchHandler::set(BlockchainBatchHandler::getAnnounceBatchDefinition());
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
