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
  public function getList($offset = NULL, $limit = NULL) {

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
  public function loadBySelfAndType($self, $blockchainTypeId) {

    if($node = $this->load($this->generateId($blockchainTypeId, $self))) {
      if ($node->getBlockchainTypeId() == $blockchainTypeId) {

        return $node;
      }
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function existsBySelfAndType($self, $blockchainTypeId) {

    return (bool) $this->loadBySelfAndType($self, $blockchainTypeId);
  }

  /**
   * {@inheritdoc}
   */
  public function create($blockchainType, $self, $addressSource,  $address, $port = NULL, $secure = NULL, $label = NULL, $save = TRUE) {

    /** @var BlockchainNodeInterface $blockchainNode */
    $blockchainNode = $this->getStorage()->create();
    $label = $label? $label : $self;
    $blockchainNode
      ->setBlockchainTypeId($blockchainType)
      ->setSelf($self)
      ->setId($this->generateId($blockchainType, $self))
      ->setLabel($label? $label : $self)
      ->setAddress($address)
      ->setSecure($secure)
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

    if ($request->hasSelfUrl()) {

      return $this->create(
        $request->getTypeParam(),
        $request->getSelfParam(),
        BlockchainNodeInterface::ADDRESS_SOURCE_CLIENT,
        $request->getSelfUrl(),
        NULL, NULL, NULL, $save);
    }

    return $this->create(
      $request->getTypeParam(),
      $request->getSelfParam(),
      BlockchainNodeInterface::ADDRESS_SOURCE_REQUEST,
      $request->getIp(),
      $request->getPort(),
      $request->isSecure(), NULL, $save);
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

  /**
   * {@inheritdoc}
   */
  public function save(BlockchainNodeInterface $blockchainNode) {
    try {
      $this->getStorage()->save($blockchainNode);
      return TRUE;
    }
    catch (\Exception $exception) {
      return FALSE;
    }
  }

  /**
   * Id generator.
   *
   * @param string $blockchainTypeId
   *   Type of blockchain.
   * @param string $self
   *   Self id param.
   *
   * @return string
   *   Hash.
   */
  public function generateId($blockchainTypeId, $self) {

    return sha1($blockchainTypeId . '_' . $self);
  }
}
