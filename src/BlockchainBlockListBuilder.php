<?php

namespace Drupal\blockchain;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Blockchain Block entities.
 *
 * @ingroup blockchain
 */
class BlockchainBlockListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Blockchain Block ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\blockchain\Entity\BlockchainBlock */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.blockchain_block.edit_form',
      ['blockchain_block' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
