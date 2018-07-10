<?php

namespace Drupal\blockchain;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Blockchain Node entities.
 */
class BlockchainNodeListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  protected $limit = 20;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {

    $header['id'] = $this->t('Id');
    $header['label'] = $this->t('Label');
    $header['blockchain_type'] = $this->t('Blockchain type');
    $header['self_id'] = $this->t('Self id');
    $header['endpoint'] = $this->t('Endpoint');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {

    /** @var \Drupal\blockchain\Entity\BlockchainNodeInterface $entity */
    $row['id'] = $entity->id();
    $row['label'] = $entity->label();
    $row['blockchain_type'] = $entity->getBlockchainTypeId();
    $row['self_id'] = $entity->getSelf();
    $row['endpoint'] = $entity->getEndPoint();

    return $row + parent::buildRow($entity);
  }

}
