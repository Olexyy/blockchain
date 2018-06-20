<?php

namespace Drupal\blockchain\Form;

use Drupal\blockchain\Service\BlockchainConfigServiceInterface;
use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
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
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * BlockchainBlockSettingsForm constructor.
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
      elseif ($element['#context'] == 'put_generic_block') {
        $genericBlock = $this->blockchainService->getStorageService()->getGenericBlock();
        $this->blockchainService->getStorageService()->save($genericBlock);
      }
    }
    else {
      $config = $this->blockchainService->getConfigService()->getConfig(TRUE);
      foreach (BlockchainConfigServiceInterface::KEYS as $key) {
        if ($form_state->hasValue($key)) {
          $config->set($key, $form_state->getValue($key));
        }
      }
      $config->save();
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

    $this->blockchainService->getConfigService()->setCurrentBlockchainConfig('blockchain_block');
    $blockchainType = $this->blockchainService->getConfigService()->getBlockchainType();
    $poolManagement = $this->blockchainService->getConfigService()->getPoolManagement();
    $announceManagement = $this->blockchainService->getConfigService()->getAnnounceManagement();
    $powPosition = $this->blockchainService->getConfigService()->getPowPosition();
    $powExpression = $this->blockchainService->getConfigService()->getPowExpression();
    $blockchainId = $this->blockchainService->getConfigService()->getBlockchainId();
    $blockchainNodeId = $this->blockchainService->getConfigService()->getBlockchainNodeId();
    $intervalPool = $this->blockchainService->getConfigService()->getIntervalPool();
    $intervalAnnounce = $this->blockchainService->getConfigService()->getIntervalAnnounce();
    $anyBlock = $this->blockchainService->getStorageService()->anyBlock();
    $blockchainAuth = $this->blockchainService->getConfigService()->isBlockchainAuth();
    $blockchainFilterType = $this->blockchainService->getConfigService()->getBlockchainFilterType();
    $blockchainFilterList = $this->blockchainService->getConfigService()->getBlockchainFilterList();
    $allowNotSecure = $this->blockchainService->getConfigService()->getAllowNotSecure();

    $form['blockchainId'] = [
      '#type' => 'textfield',
      '#default_value' => $blockchainId,
      '#title' => $this->t('Blockchain id'),
      '#description' => $this->t('Blockchain id for this blockchain.'),
      '#disabled' => $anyBlock,
    ];

    $form['blockchainNodeId'] = [
      '#type' => 'textfield',
      '#default_value' => $blockchainNodeId,
      '#description' => $this->t('Blockchain node id is used as author for mined blocks.'),
      '#title' => $this->t('Blockchain node id'),
      '#disabled' => $anyBlock,
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

    $form['blockchainAuth'] = [
      '#type' => 'checkbox',
      '#default_value' => $blockchainAuth,
      '#title' => $this->t('Auth token enabled'),
      '#description' => $this->t('Does API use token Blockchain token to interact.'),
      '#states' => [
        'visible' => [
          ':input[name="blockchainType"]' => [
            'value' => BlockchainConfigServiceInterface::TYPE_MULTIPLE
          ],
        ],
      ],
    ];

    $form['blockchainFilterType'] = [
      '#type' => 'select',
      '#title' => $this->t('Blockchain nodes filtering type'),
      '#options' => [
        BlockchainConfigServiceInterface::FILTER_TYPE_BLACKLIST => $this->t('Blacklist'),
        BlockchainConfigServiceInterface::FILTER_TYPE_WHITELIST => $this->t('Whitelist'),
      ],
      '#default_value' => $blockchainFilterType,
      '#description' => $this->t('The way, blockchain nodes will be filtered.'),
      '#states' => [
        'visible' => [
          ':input[name="blockchainType"]' => [
            'value' => BlockchainConfigServiceInterface::TYPE_MULTIPLE
          ],
        ],
      ],
    ];

    $form['blockchainFilterList'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Blockchain filter list'),
      '#default_value' => $blockchainFilterList,
      '#description' => $this->t('List of ip addresses to be filtered, newline separated.'),
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
          ':input[name="poolManagement"]' => [
            'value' => BlockchainConfigServiceInterface::POOL_MANAGEMENT_CRON
          ],
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

    $form['allowNotSecure'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow not secure protocol'),
      '#default_value' => $allowNotSecure,
      '#description' => $this->t('Check to allow not secure protocol for blockchain nodes.'),
    ];

    $form['powPosition'] = [
      '#disabled' => $anyBlock,
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
      '#disabled' => $anyBlock,
      '#type' => 'textfield',
      '#title' => $this->t('Proof of work expression'),
      '#default_value' => $powExpression,
      '#description' => $this->t('Proof of work expression in previous hash.'),
    ];

    $currentDataHandler = $this->blockchainService->getConfigService()->getConfig()->get('dataHandler');
    $form['dataHandler'] = [
      '#type' => 'select',
      '#required' => TRUE,
      '#title' => $this->t('Blockchain data handler.'),
      '#options' => $this->blockchainService->getDataManager()->getList(),
      '#default_value' => $currentDataHandler,
      '#description' => $this->t('Select data handler for given blockchain.'),
    ];

    $hasSettings = $this->blockchainService
      ->getDataManager()
      ->definitionGet($currentDataHandler, 'settings');
    if ($hasSettings) {
      $form['dataHandlerSettings'] = [
        '#type' => 'link',
        '#title' => $this->t('Data handler settings'),
        '#url' => Url::fromRoute('<current>'),
      ];
    }

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Save'),
      ],
    ];

    if (!$anyBlock) {
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
      $form['actions']['put_generic_block'] = [
        '#type' => 'button',
        '#executes_submit_callback' => TRUE,
        '#value' => $this->t('Put generic block'),
        '#context' => 'put_generic_block',
      ];
    }

    return $form;
  }

}
