<?php


namespace Drupal\blockchain\Service;

/**
 * Interface BlockchainLockerServiceInterface.
 *
 * @package Drupal\blockchain\Service
 */
interface BlockchainLockerServiceInterface {

  /**
   * Tries to lock against given name.
   *
   * @param string $lockName
   *   Lock name.
   *
   * @return bool
   *   Result.
   */
  public function lock($lockName);

  /**
   * Releases lock by name.
   *
   * @param string $lockName
   *   Lock name.
   */
  public function release($lockName);

  /**
   * Waits for lock to release given time.
   *
   * @param string $lockName
   *   Lock name.
   * @param int $timeout
   *   Timeout.
   *
   * @return bool
   *   Result.
   */
  public function wait($lockName, $timeout);

}
