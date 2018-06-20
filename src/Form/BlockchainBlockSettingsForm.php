<?php

namespace Drupal\blockchain\Form;

use Drupal\blockchain\Service\BlockchainConfigServiceInterface;
use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
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

    $form = [];

    return $form;
  }

}
