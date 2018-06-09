<?php


namespace Drupal\blockchain\Service;


use Drupal\Core\Lock\LockBackendInterface;

/**
 * Class BlockchainLockerService.
 *
 * @package Drupal\blockchain\Service
 */
class BlockchainLockerService implements BlockchainLockerServiceInterface {

  /**
   * Locker.
   *
   * @var LockBackendInterface
   */
  protected $lockBackend;

  /**
   * BlockchainLockerService constructor.
   *
   * @param LockBackendInterface $lockBackend
   *   Locker.
   */
  public function __construct(LockBackendInterface $lockBackend) {

    $this->lockBackend = $lockBackend;
  }

  /**
   * {@inheritdoc}
   */
  public function lock($lockName) {

    return $this->lockBackend->acquire($lockName);
  }

  /**
   * {@inheritdoc}
   */
  public function release($lockName) {

    $this->lockBackend->release($lockName);
  }

  /**
   * {@inheritdoc}
   */
  public function wait($lockName, $timeout) {

    return $this->lockBackend->wait($lockName, $timeout);
  }

  /**
   * {@inheritdoc}
   */
  public function lockAnnounce() {

    return $this->lock(static::ANNOUNCE);
  }

  /**
   * {@inheritdoc}
   */
  public function releaseAnnounce() {

    $this->lockBackend->release(static::ANNOUNCE);
  }

  /**
   * {@inheritdoc}
   */
  public function waitAnnounce($timeout) {

    return $this->wait(static::ANNOUNCE, $timeout);
  }

}
