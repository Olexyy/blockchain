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
    return 'blockchainblock_settings';
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

    $keys = [
      'type', 'pool_management', 'interval_pool','announce_management',
      'interval_announce', 'pof_position', 'pof_expression',
    ];
    foreach ($keys as $key) {
      if ($form_state->hasValue($key)) {
      $this->blockchainService->getConfigService()->getConfig(TRUE)
        ->set($key, $form_state->getValue($key))->save();
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

    $form['blockchainblock_settings']['#markup'] = 'Settings form for Blockchain Block entities. Manage field settings here.';
    $blockchainType = $this->blockchainService->getConfigService()->getConfig()->get('type');
    $poolManagement = $this->blockchainService->getConfigService()->getConfig()->get('pool_management');
    $announce_management = $this->blockchainService->getConfigService()->getConfig()->get('announce_management');
    $pof_position = $this->blockchainService->getConfigService()->getConfig()->get('pof_position');
    $pof_expression = $this->blockchainService->getConfigService()->getConfig()->get('pof_expression');

    $form['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Blockchain type'),
      '#options' => [
        BlockchainConfigServiceInterface::TYPE_SINGLE => $this->t('Single'),
        BlockchainConfigServiceInterface::TYPE_MULTIPLE  => $this->t('Multiple'),
      ],
      '#default_value' => $blockchainType? $blockchainType :
        BlockchainConfigServiceInterface::TYPE_SINGLE,
      '#description' => $this->t('Single means only one node, thus one blockchain database.'),
    ];

    // TODO: id;
    // TODO: immutable settings if any item;

    $form['pool_management'] = [
      '#type' => 'select',
      '#title' => $this->t('Pool management'),
      '#options' => [
        BlockchainConfigServiceInterface::POOL_MANAGEMENT_MANUAL => $this->t('Manual'),
        BlockchainConfigServiceInterface::POOL_MANAGEMENT_CRON  => $this->t('CRON'),
      ],
      '#default_value' => $poolManagement? $poolManagement :
        BlockchainConfigServiceInterface::POOL_MANAGEMENT_MANUAL,
      '#description' => $this->t('The way, pool queue will be managed.'),
    ];

    $form['interval_pool'] = [
      '#type' => 'number',
      '#title' => $this->t('Pool management interval'),
      '#default_value' => BlockchainConfigServiceInterface::INTERVAL_DEFAULT,
      '#required' => TRUE,
      '#min' => 1,
      '#description' => $this->t('Interval for pool management CRON job.'),
      '#states' => [
        'visible' => [
          ':input[name="pool_management"]' => ['value' => BlockchainConfigServiceInterface::POOL_MANAGEMENT_CRON],
        ],
      ],
    ];

    $form['announce_management'] = [
      '#type' => 'select',
      '#title' => $this->t('Announce management'),
      '#options' => [
        BlockchainConfigServiceInterface::ANNOUNCE_MANAGEMENT_IMMEDIATE => $this->t('Immediate'),
        BlockchainConfigServiceInterface::ANNOUNCE_MANAGEMENT_CRON  => $this->t('CRON'),
      ],
      '#default_value' => $announce_management? $announce_management :
        BlockchainConfigServiceInterface::POOL_MANAGEMENT_MANUAL,
      '#description' => $this->t('The way, announce queue will be managed.'),
    ];

    $form['interval_announce'] = [
      '#type' => 'number',
      '#title' => $this->t('Announce management interval'),
      '#default_value' => BlockchainConfigServiceInterface::INTERVAL_DEFAULT,
      '#required' => TRUE,
      '#min' => 1,
      '#description' => $this->t('Interval for announce management CRON job.'),
      '#states' => [
        'visible' => [
          ':input[name="announce_management"]' => ['value' => BlockchainConfigServiceInterface::ANNOUNCE_MANAGEMENT_CRON],
        ],
      ],
    ];

    $form['pof_position'] = [
      '#type' => 'select',
      '#title' => $this->t('Proof of work position'),
      '#options' => [
        BlockchainConfigServiceInterface::POF_POSITION_START => $this->t('Start'),
        BlockchainConfigServiceInterface::POF_POSITION_END  => $this->t('End'),
      ],
      '#default_value' => $pof_position? $pof_position :
        BlockchainConfigServiceInterface::POF_POSITION_START,
      '#description' => $this->t('Proof of work position in previous hash.'),
    ];

    $form['pof_expression'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Proof of work expression'),
      '#default_value' => $pof_expression? $pof_expression :
        BlockchainConfigServiceInterface::POF_EXPRESSION,
      '#description' => $this->t('Proof of work expression in previous hash.'),
    ];

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Submit'),
      ],
    ];

    return $form;
  }

}
