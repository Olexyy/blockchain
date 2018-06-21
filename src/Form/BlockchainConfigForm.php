<?php

namespace Drupal\blockchain\Form;

use Drupal\blockchain\Entity\BlockchainConfig;
use Drupal\blockchain\Entity\BlockchainConfigInterface;
use Drupal\blockchain\Service\BlockchainConfigServiceInterface;
use Drupal\blockchain\Service\BlockchainService;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Class BlockchainConfigForm.
 */
class BlockchainConfigForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {

    $form = parent::form($form, $form_state);
    /** @var BlockchainConfig $blockchainConfig */
    $blockchainConfig = $this->entity;
    // TODO INJECT THIS
    BlockchainService::instance()->getConfigService()->setCurrentConfig($blockchainConfig->id());
    $anyBlock = BlockchainService::instance()->getStorageService()->anyBlock();

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $blockchainConfig->label(),
      '#description' => $this->t("Label for the Blockchain config."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $blockchainConfig->id(),
      '#machine_name' => [
        'exists' => '\Drupal\blockchain\Entity\BlockchainConfig::load',
      ],
      '#disabled' => !$blockchainConfig->isNew(),
    ];

    $form['blockchainId'] = [
      '#type' => 'textfield',
      '#default_value' => $blockchainConfig->getBlockchainId(),
      '#title' => $this->t('Blockchain id'),
      '#description' => $this->t('Blockchain id for this blockchain.'),
      '#disabled' => $anyBlock,
    ];

    $form['nodeId'] = [
      '#type' => 'textfield',
      '#default_value' => $blockchainConfig->getNodeId(),
      '#description' => $this->t('Blockchain node id is used as author for mined blocks.'),
      '#title' => $this->t('Blockchain node id'),
      '#disabled' => $anyBlock,
    ];

    $form['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Blockchain type'),
      '#options' => [
        BlockchainConfigInterface::TYPE_SINGLE => $this->t('Single'),
        BlockchainConfigInterface::TYPE_MULTIPLE  => $this->t('Multiple'),
      ],
      '#default_value' => $blockchainConfig->getType(),
      '#description' => $this->t('Single means only one node, thus one blockchain database.'),
    ];

    $form['isAuth'] = [
      '#type' => 'checkbox',
      '#default_value' => $blockchainConfig->getIsAuth(),
      '#title' => $this->t('Auth token enabled'),
      '#description' => $this->t('Does API use token Blockchain token to interact.'),
      '#states' => [
        'visible' => [
          ':input[name="blockchainType"]' => [
            'value' => BlockchainConfigInterface::TYPE_MULTIPLE
          ],
        ],
      ],
    ];

    $form['filterType'] = [
      '#type' => 'select',
      '#title' => $this->t('Blockchain nodes filtering type'),
      '#options' => [
        BlockchainConfigInterface::FILTER_TYPE_BLACKLIST => $this->t('Blacklist'),
        BlockchainConfigInterface::FILTER_TYPE_WHITELIST => $this->t('Whitelist'),
      ],
      '#default_value' => $blockchainConfig->getFilterType(),
      '#description' => $this->t('The way, blockchain nodes will be filtered.'),
      '#states' => [
        'visible' => [
          ':input[name="blockchainType"]' => [
            'value' => BlockchainConfigInterface::TYPE_MULTIPLE
          ],
        ],
      ],
    ];

    $form['filterList'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Blockchain filter list'),
      '#default_value' => $blockchainConfig->getFilterList(),
      '#description' => $this->t('List of ip addresses to be filtered, newline separated.'),
    ];

    $form['poolManagement'] = [
      '#type' => 'select',
      '#title' => $this->t('Pool management'),
      '#options' => [
        BlockchainConfigInterface::POOL_MANAGEMENT_MANUAL => $this->t('Manual'),
        BlockchainConfigInterface::POOL_MANAGEMENT_CRON  => $this->t('CRON'),
      ],
      '#default_value' => $blockchainConfig->getPoolManagement(),
      '#description' => $this->t('The way, pool queue will be managed.'),
    ];

    $form['intervalPool'] = [
      '#type' => 'number',
      '#title' => $this->t('Pool management interval'),
      '#default_value' => $blockchainConfig->getIntervalPool(),
      '#required' => TRUE,
      '#min' => 1,
      '#description' => $this->t('Interval for pool management CRON job.'),
      '#states' => [
        'visible' => [
          ':input[name="poolManagement"]' => [
            'value' => BlockchainConfigInterface::POOL_MANAGEMENT_CRON
          ],
        ],
      ],
    ];

    $form['announceManagement'] = [
      '#type' => 'select',
      '#title' => $this->t('Announce management'),
      '#options' => [
        BlockchainConfigInterface::ANNOUNCE_MANAGEMENT_IMMEDIATE => $this->t('Immediate'),
        BlockchainConfigInterface::ANNOUNCE_MANAGEMENT_CRON  => $this->t('CRON'),
      ],
      '#default_value' => $blockchainConfig->getAnnounceManagement(),
      '#description' => $this->t('The way, announce queue will be managed.'),
    ];

    $form['intervalAnnounce'] = [
      '#type' => 'number',
      '#title' => $this->t('Announce management interval'),
      '#default_value' => $blockchainConfig->getIntervalAnnounce(),
      '#required' => TRUE,
      '#min' => 1,
      '#description' => $this->t('Interval for announce management CRON job.'),
      '#states' => [
        'visible' => [
          ':input[name="announceManagement"]' => ['value' => BlockchainConfigInterface::ANNOUNCE_MANAGEMENT_CRON],
        ],
      ],
    ];

    $form['allowNotSecure'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow not secure protocol'),
      '#default_value' => $blockchainConfig->getAllowNotSecure(),
      '#description' => $this->t('Check to allow not secure protocol for blockchain nodes.'),
    ];

    $form['powPosition'] = [
      '#disabled' => $anyBlock,
      '#type' => 'select',
      '#title' => $this->t('Proof of work position'),
      '#options' => [
        BlockchainConfigInterface::POW_POSITION_START => $this->t('Start'),
        BlockchainConfigInterface::POW_POSITION_END  => $this->t('End'),
      ],
      '#default_value' => $blockchainConfig->getPowPosition(),
      '#description' => $this->t('Proof of work position in previous hash.'),
    ];

    $form['powExpression'] = [
      '#disabled' => $anyBlock,
      '#type' => 'textfield',
      '#title' => $this->t('Proof of work expression'),
      '#default_value' => $blockchainConfig->getPowExpression(),
      '#description' => $this->t('Proof of work expression in previous hash.'),
    ];

    $form['dataHandler'] = [
      '#type' => 'select',
      '#required' => TRUE,
      '#title' => $this->t('Blockchain data handler.'),
      '#options' => BlockchainService::instance()->getDataManager()->getList(),
      '#default_value' => $blockchainConfig->getDataHandler(),
      '#description' => $this->t('Select data handler for given blockchain.'),
    ];

    $hasSettings = BlockchainService::instance()
      ->getDataManager()
      ->definitionGet($blockchainConfig->getDataHandler(), 'settings');
    if ($hasSettings) {
      $form['dataHandlerSettings'] = [
        '#type' => 'link',
        '#title' => $this->t('Data handler settings'),
        '#url' => Url::fromRoute('<current>'),
      ];
    }

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

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {

    $blockchain_config = $this->entity;
    $status = $blockchain_config->save();
    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('Created the %label Blockchain config.', [
          '%label' => $blockchain_config->label(),
        ]));
        break;

      default:
        $this->messenger()->addStatus($this->t('Saved the %label Blockchain config.', [
          '%label' => $blockchain_config->label(),
        ]));
    }
    $form_state->setRedirectUrl($blockchain_config->toUrl('collection'));
  }

}
