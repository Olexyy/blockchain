<?php

namespace Drupal\blockchain\Service;

use Drupal\blockchain\Entity\BlockchainNode;
use Drupal\blockchain\Entity\BlockchainNodeInterface;
use Drupal\blockchain\Utils\BlockchainRequestInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Class BlockchainNodeService.
 *
 * @package Drupal\blockchain\Service
 */
class BlockchainNodeService implements BlockchainNodeServiceInterface {

  /**
   * Entity type manager.
   *
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * BlockchainNodeService constructor.
   *
   * @param EntityTypeManagerInterface $entityTypeManager
   *   Entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {

    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public function getStorage() {

    try {
      return $this->entityTypeManager
        ->getStorage(BlockchainNode::entityTypeId());
    } catch (\Exception $exception) {
      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getList($offset = 0, $limit = 10) {

    $list = $this->getStorage()
      ->getQuery()
      ->accessCheck(FALSE)
      ->range($offset, $limit)
      ->execute();

    return $this->getStorage()->loadMultiple($list);
  }

  /**
   * {@inheritdoc}
   */
  public function getCount() {

    return $this->getStorage()
      ->getQuery()
      ->accessCheck(FALSE)
      ->count()
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function exists($id) {

    return (bool) $this->load($id);
  }

  /**
   * {@inheritdoc}
   */
  public function load($id) {

    return $this->getStorage()->load($id);
  }

  /**
   * {@inheritdoc}
   */
  public function create($id, $label, $ip, $port, $save = TRUE) {

    /** @var BlockchainNodeInterface $blockchainNode */
    $blockchainNode = $this->getStorage()->create();
    $blockchainNode
      ->setId($id)
      ->setLabel($label)
      ->setIp($ip)
      ->setPort($port);
    try {
      if ($save) {
        $this->getStorage()->save($blockchainNode);
      }

      return $blockchainNode;
    } catch (\Exception $exception) {

      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function createFromRequest(BlockchainRequestInterface $request, $save = TRUE) {

    return $this->create(
      $request->getSelfParam(),
      $request->getSelfParam(),
      $request->getIp(),
      $request->getPort(), $save);
  }

  /**
   * {@inheritdoc}
   */
  public function delete(BlockchainNodeInterface $blockchainNode) {
    try {
      $this->getStorage()->delete([$blockchainNode]);
      return TRUE;
    }
    catch (\Exception $exception) {
      return FALSE;
    }
  }

}
