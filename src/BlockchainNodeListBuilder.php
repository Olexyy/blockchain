<?php

namespace Drupal\blockchain;

use Drupal\blockchain\Entity\BlockchainNodeInterface;
use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Blockchain Node entities.
 */
class BlockchainNodeListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {

    $header['id'] = $this->t('Id');
    $header['label'] = $this->t('Label');
    $header['endpoint'] = $this->t('Endpoint');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {

    /** @var BlockchainNodeInterface $entity */
    $row['id'] = $entity->id();
    $row['label'] = $entity->label();
    $row['endpoint'] = $entity->getEndPoint();

    return $row + parent::buildRow($entity);
  }

}
