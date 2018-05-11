<?php

namespace Drupal\blockchain\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class BlockchainDataInterface.
 *
 * @package Drupal\blockchain\Plugin
 */
interface BlockchainDataInterface extends PluginInspectionInterface {

  const LOGGER_CHANNEL = 'blockchain_queue';
  const QUEUE = 'blockchain_queue';
  const DATA_KEY = 'blockchainBlockData';

  /**
   * Setter for data.
   *
   * This may be object or just string.
   *
   * @param mixed $data
   *   Some data to be set.
   *
   * @return bool
   *   Execution result.
   */
  public function setData($data);

  /**
   * Getter for data.
   *
   * This may be object or just string.
   *
   * @return mixed
   *   Some object.
   */
  public function getData();

  /**
   * Getter for raw data.
   *
   * This is always string in format {plugin_id}::{data}.
   *
   * @return string
   *   May be also empty string if value not set.
   */
  public function getRawData();

  /**
   * Getter for form widget.
   *
   * @return array
   *   Render array.
   */
  public function getWidget();

  /**
   * Getter for element view.
   *
   * @return array
   *   Render array.
   */
  public function getFormatter();

  /**
   * Manages logic for moving values from form state to items.
   *
   * @param FieldItemListInterface $items
   *   Field items.
   * @param array $form
   *   Form render array.
   * @param FormStateInterface $form_state
   *   Form state object.
   */
  public function extractFormValues(FieldItemListInterface $items, array $form, FormStateInterface $form_state);

}