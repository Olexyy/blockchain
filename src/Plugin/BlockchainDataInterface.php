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

  /**
   * Setter for data.
   *
   * @param mixed $data
   *   SOme dat to be set.
   *
   * @return string|false
   *   Serialized data or false.
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
   * Getter for form widget.
   *
   * @return array
   *   Render array.
   */
  public function getWidget();

  /**
   * Extracts and submits value.
   *
   * @param FormStateInterface $formState
   *   Form state.
   */
  public function setSubmitData(FormStateInterface $formState);

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