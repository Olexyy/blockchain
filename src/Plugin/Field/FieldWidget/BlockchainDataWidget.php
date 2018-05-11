<?php

namespace Drupal\blockchain\Plugin\Field\FieldWidget;

use Drupal\blockchain\Entity\BlockchainBlockInterface;
use Drupal\blockchain\Service\BlockchainServiceInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'blockchain_data_widget' widget.
 *
 * @FieldWidget(
 *   id = "blockchain_data_widget",
 *   label = @Translation("Blockchain data widget"),
 *   field_types = {
 *     "blockchain_data"
 *   }
 * )
 */
class BlockchainDataWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * Blockchain service.
   *
   * @var BlockchainServiceInterface
   */
  protected $blockchainService;

  /**
   * Creates an instance of the plugin.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container to pull out services used in the plugin.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   *
   * @return static
   *   Returns an instance of this plugin.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {

    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('blockchain.service')
    );
  }

  /**
   * BlockchainDataWidget constructor.
   *
   * @param $plugin_id
   *   String plugin id.
   * @param $plugin_definition
   *   Array of plugin definition.
   * @param FieldDefinitionInterface $field_definition
   *   Field definition object.
   * @param array $settings
   *   Array of setting for given widget.
   * @param array $third_party_settings
   *   Array of third party settings.
   * @param BlockchainServiceInterface $blockchainService
   *   Blockchain service.
   */
  public function __construct($plugin_id,
                              $plugin_definition,
                              FieldDefinitionInterface $field_definition,
                              array $settings,
                              array $third_party_settings,
                              BlockchainServiceInterface $blockchainService) {

    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->blockchainService = $blockchainService;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $blockDataHandler = $this->blockchainService->getBlockDataHandler($items[$delta]->value);
    $element['value'] = $element + $blockDataHandler->getWidget();

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function extractFormValues(FieldItemListInterface $items, array $form, FormStateInterface $form_state) {

    parent::extractFormValues($items, $form, $form_state);
    $blockDataHandler = $this->blockchainService->getBlockDataHandler();
    foreach ($items as $key => $item) {
      $blockDataHandler->setData($item->value);
      $item->value = $blockDataHandler->getRawData();
    }

  }

}
