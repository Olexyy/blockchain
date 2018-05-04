<?php

namespace Drupal\blockchain\Plugin\BlockchainData;

use Drupal\blockchain\Plugin\BlockchainDataBase;

/**
 * BlockchainBlockData as simple string.
 *
 * @BlockchainData(
 *  id = "simple",
 *  label = @Translation("Simple string data")
 * )
 */
class SimpleBlockchainData extends BlockchainDataBase {

  /**
   * {@inheritdoc}
   */
  public function setData($data) {

    if (is_string($data)) {
      $this->blockchainBlock->setData($data);
      return TRUE;
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {

    return $this->blockchainBlock->getData();
  }
}