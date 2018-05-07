<?php

namespace Drupal\blockchain\Form;

use Drupal\blockchain\Service\BlockchainConfigServiceInterface;
use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BlockchainBlockSettingsForm.
 *
 * @ingroup blockchain
 */
class BlockchainBlockSettingsForm extends FormBase {

  /**
   * Blockchain service.
   *
   * @var BlockchainServiceInterface
   */
  protected $blockchainService;

  /**
   * BlockchainBlockSettingsForm constructor.
   *
   * @param BlockchainServiceInterface $blockchainService
   *   Blockchain service.
   */
  public function __construct(BlockchainServiceInterface $blockchainService) {

    $this->blockchainService = $blockchainService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {

    return new static(
      $container->get('blockchain.service')
    );
  }

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'blockchain_block_settings';
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

    $element = $form_state->getTriggeringElement();
    if ($element['#type'] == 'button') {
      if ($element['#context'] == 'regenerate_blockchain_id') {
        $this->blockchainService->getConfigService()->setBlockchainId();
      }
      elseif ($element['#context'] == 'regenerate_blockchain_node_id') {
        $this->blockchainService->getConfigService()->setBlockchainNodeId();
      }
    }
    else {
      foreach (BlockchainConfigServiceInterface::KEYS as $key) {
        if ($form_state->hasValue($key)) {
          $this->blockchainService->getConfigService()->getConfig(TRUE)
            ->set($key, $form_state->getValue($key))->save();
        }
      }
    }

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

    $blockchainType = $this->blockchainService->getConfigService()->getBlockchainType();
    $poolManagement = $this->blockchainService->getConfigService()->getPoolManagement();
    $announceManagement = $this->blockchainService->getConfigService()->getAnnounceManagement();
    $powPosition = $this->blockchainService->getConfigService()->getPowPosition();
    $powExpression = $this->blockchainService->getConfigService()->getPowExpression();
    $blockchainId = $this->blockchainService->getConfigService()->getBlockchainId();
    $blockchainNodeId = $this->blockchainService->getConfigService()->getBlockchainNodeId();
    $intervalPool = $this->blockchainService->getConfigService()->getIntervalPool();
    $intervalAnnounce = $this->blockchainService->getConfigService()->getIntervalAnnounce();

    $form['blockchainId'] = [
      '#type' => 'item',
      '#description' => $blockchainId,
      '#title' => $this->t('Blockchain id'),
    ];

    $form['blockchainNodeId'] = [
      '#type' => 'item',
      '#description' => $blockchainNodeId,
      '#title' => $this->t('Blockchain node id'),
    ];

    $form['blockchainType'] = [
      '#type' => 'select',
      '#title' => $this->t('Blockchain type'),
      '#options' => [
        BlockchainConfigServiceInterface::TYPE_SINGLE => $this->t('Single'),
        BlockchainConfigServiceInterface::TYPE_MULTIPLE  => $this->t('Multiple'),
      ],
      '#default_value' => $blockchainType,
      '#description' => $this->t('Single means only one node, thus one blockchain database.'),
    ];

    $form['poolManagement'] = [
      '#type' => 'select',
      '#title' => $this->t('Pool management'),
      '#options' => [
        BlockchainConfigServiceInterface::POOL_MANAGEMENT_MANUAL => $this->t('Manual'),
        BlockchainConfigServiceInterface::POOL_MANAGEMENT_CRON  => $this->t('CRON'),
      ],
      '#default_value' => $poolManagement,
      '#description' => $this->t('The way, pool queue will be managed.'),
    ];

    $form['intervalPool'] = [
      '#type' => 'number',
      '#title' => $this->t('Pool management interval'),
      '#default_value' => $intervalPool,
      '#required' => TRUE,
      '#min' => 1,
      '#description' => $this->t('Interval for pool management CRON job.'),
      '#states' => [
        'visible' => [
          ':input[name="poolManagement"]' => ['value' => BlockchainConfigServiceInterface::POOL_MANAGEMENT_CRON],
        ],
      ],
    ];

    $form['announceManagement'] = [
      '#type' => 'select',
      '#title' => $this->t('Announce management'),
      '#options' => [
        BlockchainConfigServiceInterface::ANNOUNCE_MANAGEMENT_IMMEDIATE => $this->t('Immediate'),
        BlockchainConfigServiceInterface::ANNOUNCE_MANAGEMENT_CRON  => $this->t('CRON'),
      ],
      '#default_value' => $announceManagement,
      '#description' => $this->t('The way, announce queue will be managed.'),
    ];

    $form['intervalAnnounce'] = [
      '#type' => 'number',
      '#title' => $this->t('Announce management interval'),
      '#default_value' => $intervalAnnounce,
      '#required' => TRUE,
      '#min' => 1,
      '#description' => $this->t('Interval for announce management CRON job.'),
      '#states' => [
        'visible' => [
          ':input[name="announceManagement"]' => ['value' => BlockchainConfigServiceInterface::ANNOUNCE_MANAGEMENT_CRON],
        ],
      ],
    ];

    $form['powPosition'] = [
      '#type' => 'select',
      '#title' => $this->t('Proof of work position'),
      '#options' => [
        BlockchainConfigServiceInterface::POW_POSITION_START => $this->t('Start'),
        BlockchainConfigServiceInterface::POW_POSITION_END  => $this->t('End'),
      ],
      '#default_value' => $powPosition,
      '#description' => $this->t('Proof of work position in previous hash.'),
    ];

    $form['powExpression'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Proof of work expression'),
      '#default_value' => $powExpression,
      '#description' => $this->t('Proof of work expression in previous hash.'),
    ];

    $form['dataHandler'] = [
      '#type' => 'select',
      '#required' => TRUE,
      '#title' => $this->t('Blockchain data handler.'),
      '#options' => $this->blockchainService->getBlockchainDataList(),
      '#default_value' => $this->blockchainService->getConfigService()->getConfig()->get('dataHandler'),
      '#description' => $this->t('Select data handler for given blockchain.'),
    ];

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Save'),
      ],
    ];

    if ($this->blockchainService->blockchainIsEmpty()) {
      $form['actions']['regenerate_blockchain_id'] = [
        '#type' => 'button',
        '#executes_submit_callback' => TRUE,
        '#value' => $this->t('Regenerate blockchain id'),
        '#context' => 'regenerate_blockchain_id',
      ];
      $form['actions']['regenerate_blockchain_node_id'] = [
        '#type' => 'button',
        '#executes_submit_callback' => TRUE,
        '#value' => $this->t('Regenerate blockchain node id'),
        '#context' => 'regenerate_blockchain_node_id',
      ];
    }

    return $form;
  }

}
