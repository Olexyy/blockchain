<?php

namespace Drupal\blockchain\Plugin\BlockchainData;

use Drupal\blockchain\Plugin\BlockchainDataBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;

/**
 * NodeBlockchainData based on node.
 *
 * @BlockchainData(
 *  id = "node_data",
 *  label = @Translation("Node data")
 * )
 */
class NodeBlockchainData extends BlockchainDataBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function setData($data) {

    if ($data instanceof NodeInterface) {
      $data = serialize($data);
      $this->data = $this->dataToSleep($data);
      return TRUE;
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {

    if ($this->data) {
      $data = $this->dataWakeUp($this->data);

      return unserialize($data);
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getRawData() {

    if ($this->data) {
      return $this->data;
    }

    return '';
  }

  /**
   * // todo manage bundles and fields
   *
   * {@inheritdoc}
   */
  public function getWidget() {

    $entity = $this->hasData()?  $this->getData() : Node::create(['type' => 'article']);
    $form = $this->entityFormBuilder->getForm($entity);
    foreach ($form as $key => $data) {
      if (!in_array($key, $this->keys())) {
        unset ($form[$key]);
      }
    }

    return $form;
  }

  /**
   * // todo manage bundles and fields
   *
   * {@inheritdoc}
   */
  public function getFormatter() {

    $entity = $this->hasData()?  $this->getData() : Node::create(['type' => 'article']);
    $view = $this->entityTypeManager->getViewBuilder($entity->getEntityTypeId())->view($entity);

    return $view;
  }

  /**
   * // todo manage bundles and fields
   *
   * {@inheritdoc}
   */
  public function extractFormValues(FieldItemListInterface $items, array $form, FormStateInterface $form_state) {

    $entity = Node::create(['type' => 'article']);
    foreach ($this->keys() as $key) {
      if ($form_state->hasValue($key)) {
        $entity->set($key, $form_state->getValue($key));
      }
    }

    foreach ($items as $key => $item) {
      $this->setData($entity);
      $item->value = $this->getRawData();
    }

  }

  protected function keys() {
    return ['body', 'title'];
  }

}
