<?php

namespace Drupal\blockchain\Entity;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Provides an interface for defining Blockchain Block entities.
 *
 * @ingroup blockchain
 */
interface BlockchainBlockInterface extends ContentEntityInterface {

  /**
   * Gets the Blockchain Block creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Blockchain Block.
   */
  public function getCreatedTime();

  /**
   * Sets the Blockchain Block creation timestamp.
   *
   * @param int $timestamp
   *   The Blockchain Block creation timestamp.
   *
   * @return \Drupal\blockchain\Entity\BlockchainBlockInterface
   *   The called Blockchain Block entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Getter for author name.
   *
   * @return string|null
   *   Author name,
   */
  public function getAuthor();

  /**
   * Setter for author.
   *
   * @param string|null $author
   *   Author name.
   *
   * @return \Drupal\blockchain\Entity\BlockchainBlockInterface
   *   Chaining.
   */
  function setAuthor($author);

  /**
   * Getter for data.
   *
   * @return string|null
   */
  function getData();

  /**
   * Setter for data.
   *
   * @param $data
   *   String serialised data.
   *
   * @return \Drupal\blockchain\Entity\BlockchainBlockInterface
   *   Chaining.
   */
  function setData($data);

  /**
   * Getter for nonce.
   *
   * @return string|null
   */
  function getNonce();

  /**
   * @param $nonce
   * @return \Drupal\blockchain\Entity\BlockchainBlockInterface
   *   Chaining.
   */
  function setNonce($nonce);

  /**
   * Getter for hash.
   *
   * @return string|null
   */
  function getPreviousHash();

  /**
   * Setter for hash.
   *
   * @param string $hash
   *   Hash.
   *
   * @return \Drupal\blockchain\Entity\BlockchainBlockInterface
   *   Chaining.
   */
  function setPreviousHash($hash);

  /**
   * Getter for timestamp.
   *
   * @return int|null
   *   Timestamp.
   */
  function getTimestamp();

  /**
   * Setter for timestamp.
   *
   * @param int $timestamp
   *   Timestamp.
   *
   * @return \Drupal\blockchain\Entity\BlockchainBlockInterface
   *   Chaining.
   */
  function setTimestamp($timestamp);

  /**
   * Getter for block hash from object.
   *
   * @return string
   *   Hash, provided by Util class.
   */
  public function toHash();

  /**
   * Overrides save handler definition.
   *
   * @return int
   *   Save result.
   */
  public function save();

  /**
   * Overrides deletes handler definition.
   *
   * @return mixed
   *   Delete result.
   */
  public function delete();

  /**
   * Comparator.
   *
   * @param BlockchainBlockInterface $blockchainBlock
   *   Block to compare.
   *
   * @return bool
   *   Compare result.
   */
  public function equals(BlockchainBlockInterface $blockchainBlock);

}
