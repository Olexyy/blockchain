<?php

namespace Drupal\blockchain\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;
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
  const BLOCK_KEY = 'blockchainBlock';

  /**
   * Setter for data.
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
   * @return mixed
   *   Some object.
   */
  public function getData();

  /**
   * Extracts and returns submit value.
   *
   * @param FormStateInterface $formState
   *   Form state.
   *
   * @return string
   *   Serialized value expected.
   */
  public function getSubmitData(FormStateInterface $formState);

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
   * @param mixed $data
   *   Anything.
   *
   * @return array
   *   Render array.
   */
  public function getView($data);

}