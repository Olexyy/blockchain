<?php

namespace Drupal\blockchain_emulation\Service;

use Drupal\blockchain\Entity\BlockchainNodeInterface;
use Drupal\blockchain\Service\BlockchainNodeServiceInterface;
use Drupal\blockchain\Utils\BlockchainRequestInterface;
use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\State\StateInterface;

/**
 * Class BlockchainEmulationNodeStorage.
 *
 * @package Drupal\blockchain_emulation\Service
 */
class BlockchainEmulationNodeService implements BlockchainEmulationNodeServiceInterface {

  /**
   * Entity type manager.
   *
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Blockchain node service.
   *
   * @var BlockchainNodeServiceInterface
   */
  protected $blockchainNodeService;

  /**
   * Drupal state.
   *
   * @var StateInterface
   */
  protected $state;

  /**
   * @var
   */
  protected $uuid;

  /**
   * BlockchainNodeService constructor.
   *
   * @param EntityTypeManagerInterface $entityTypeManager
   *   Entity type manager service.
   * @param BlockchainNodeServiceInterface $blockchainNodeService
   *   Blockchain node service.
   * @param StateInterface $state
   *   State service.
   * @param UuidInterface $uuid
   *   Uuid service.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager,
                              BlockchainNodeServiceInterface $blockchainNodeService,
                              StateInterface $state,
                              UuidInterface $uuid) {

    $this->entityTypeManager = $entityTypeManager;
    $this->blockchainNodeService = $blockchainNodeService;
    $this->state = $state;
    $this->uuid = $uuid;
  }

  /**
   * {@inheritdoc}
   */
  public function getStorage() {

    return $this->state->get(static::STORAGE_NAMESPACE, []);
  }

  /**
   * {@inheritdoc}
   */
  public function getList($offset = NULL, $limit = NULL) {

    $results = [];
    foreach ($this->getStorage() as $index => $node) {
      if ($offset) {
        $offset--;
        continue;
      }
      $results[]= $node;
      if ($limit && count($results) == $limit) {
        break;
      }
    }

    return $results;
  }

  /**
   * {@inheritdoc}
   */
  public function getCount() {

    return count($this->getStorage());
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

    $data = $this->getStorage();
    foreach ($data as $index => $node) {
      if ($node->getId() == $id) {

        return $node;
      }
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function create($id, $label, $ip, $port, $secure = NULL, $save = TRUE) {

    return $blockchainNode = $this->blockchainNodeService->create($id, $label, $ip, $port, $secure, $save);
  }

  /**
   * {@inheritdoc}
   */
  public function createFromRequest(BlockchainRequestInterface $request, $save = TRUE) {

    $blockchainNode = $this->blockchainNodeService->createFromRequest($request, FALSE);
    if ($save) {
      $this->save($blockchainNode);
    }

    return $blockchainNode;
  }

  /**
   * {@inheritdoc}
   */
  public function save(BlockchainNodeInterface $blockchainNode) {

    $data = $this->getStorage();
    $data[] = $blockchainNode;
    $this->state->set(static::STORAGE_NAMESPACE,  $data);

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function delete(BlockchainNodeInterface $blockchainNode) {

    $data = $this->getStorage();
    foreach ($data as $index => $node) {
      if ($node->getId() == $blockchainNode->getId()) {
        unset($data[$index]);
        $this->state->set(static::STORAGE_NAMESPACE,  $data);
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function setNodes($count) {

    $existingCount = $this->getCount();
    if ($existingCount > $count) {
      $this->removeBlocks($existingCount - $count);
    }
    else if ($existingCount < $count) {
      $this->addBlocks($count - $existingCount);
    }

  }

  /**
   * {@inheritdoc}
   */
  public function removeBlocks($count) {

    while ($count > 0) {
      $data = $this->getStorage();
      array_pop($data);
      $this->state->set(static::STORAGE_NAMESPACE, $data);
      $count--;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function addBlocks($count) {

    while ($count > 0) {
      $data = $this->getStorage();
      $data[] = $this->getRandomNode();
      $this->state->set(static::STORAGE_NAMESPACE, $data);
      $count--;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getRandomNode() {

    $uuid = $this->uuid->generate();
    $ip = mt_rand(0,255).".".mt_rand(0,255).".".mt_rand(0,255).".".mt_rand(0,255);

    return $this->create(
      $uuid, $uuid, $ip, '80', FALSE
    );
  }

}
