<?php

namespace Drupal\blockchain\Utils;

use Drupal\blockchain\Service\BlockchainService;
use Drupal\Core\Url;

/**
 * Class BlockchainBatchHelper.
 *
 * @package Drupal\blockchain\Utils
 */
class BlockchainBatchHandler {

  /**
   * Initializes and starts mining batch.
   *
   * @param string|Url $redirect
   *   Redirect location.
   * @return null|\Symfony\Component\HttpFoundation\RedirectResponse
   *   Return redirect.
   */
  public static function doMiningBatch($redirect = NULL) {

    static::set(static::getMiningBatchDefinition());

    return static ::process($redirect);
  }

  /**
   * Setup handler for batch.
   *
   * @param array $definition
   *   Definition for batch.
   */
  public static function set(array $definition) {

    batch_set($definition);
  }

  /**
   * Starts batch processing.
   *
   * @param string|Url $redirect
   *   Redirect location.
   * @return null|\Symfony\Component\HttpFoundation\RedirectResponse
   *   Return redirect.
   */
  public static function process($redirect = NULL) {

    return batch_process($redirect);
  }

  /**
   * Mining batch definition.
   *
   * @return array
   *   Definition for batch.
   */
  public static function getMiningBatchDefinition() {

    $batch = [
      'title' => t('Mining block...'),
      'operations' => [
        [ static::class . '::processMiningBatch', [] ],
      ],
      'finished' => static::class .'::finalizeMiningBatch',
    ];

    return $batch;
  }

  /**
   * Batch processor.
   *
   * @param mixed $context
   *   Batch context.
   */
  public static function processMiningBatch(array &$context) {

    $blockchainService = BlockchainService::instance();
    $message = t('Mining is in progress...');
    $results = $context['results']? $context['results'] : [];
    $results[] = $blockchainService->getQueueService()->doMining();
    $context['message'] = $message;
    $context['results'] = $results;
  }

  /**
   * Batch finalizer.
   *
   * {@inheritdoc}
   */
  public static function finalizeMiningBatch($success, $results, $operations) {

    if ($success) {
      $message = t('@count blocks processed.', [
        '@count' => count($results),
      ]);
    }
    else {
      $message = t('Finished with an error.');
    }
    \Drupal::messenger()->addStatus($message);
  }

}