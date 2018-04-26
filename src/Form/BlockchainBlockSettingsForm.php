<?php

namespace Drupal\blockchain\Form;

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
    $this->blockchainService->getConfigService()->getConfig(TRUE)
      ->set('type', $form_state->getValue('type'))->save();
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

    $form['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Blockchain type'),
      '#options' => [
        'single' => $this->t('Single'),
        'distributed' => $this->t('Distributed'),
      ],
      '#default_value' => $blockchainType? $blockchainType : 'single',
    ];

    return $form;
  }

}
