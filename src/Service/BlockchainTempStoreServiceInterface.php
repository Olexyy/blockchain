<?php
/**
 * Created by PhpStorm.
 * User: oos
 * Date: 06.07.18
 * Time: 1:26
 */

namespace Drupal\blockchain\Service;


interface BlockchainTempStoreServiceInterface {

  const LOGGER_CHANNEL = 'blockchain.tempstore';
  const STORAGE_PREFIX = 'blockchain_tempstore_';
  const BLOCKS_KEY = 'blocks';

}