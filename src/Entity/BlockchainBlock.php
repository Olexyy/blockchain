<?php

namespace Drupal\blockchain\Entity;


use Drupal\blockchain\Utils\Util;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the Blockchain Block entity.
 *
 * @ingroup blockchain
 *
 * @ContentEntityType(
 *   id = "blockchain_block",
 *   label = @Translation("Blockchain Block"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\blockchain\BlockchainBlockListBuilder",
 *     "views_data" = "Drupal\blockchain\Entity\BlockchainBlockViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\blockchain\Form\BlockchainBlockForm",
 *       "add" = "Drupal\blockchain\Form\BlockchainBlockForm",
 *       "edit" = "Drupal\blockchain\Form\BlockchainBlockForm",
 *       "delete" = "Drupal\blockchain\Form\BlockchainBlockDeleteForm",
 *     },
 *     "access" = "Drupal\blockchain\BlockchainBlockAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\blockchain\BlockchainBlockHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "blockchain_block",
 *   admin_permission = "administer blockchain block entities",
 *   entity_keys = {
 *     "id" = "id",
 *   },
 *   links = {
 *     "canonical" = "/blockchain/block/blockchain_block/{blockchain_block}",
 *     "add-form" = "/blockchain/block/blockchain_block/add",
 *     "edit-form" = "/blockchain/block/blockchain_block/{blockchain_block}/edit",
 *     "delete-form" = "/blockchain/block/blockchain_block/{blockchain_block}/delete",
 *     "collection" = "/blockchain/block/blockchain_block",
 *   },
 *   field_ui_base_route = "blockchain_block.settings"
 * )
 */
class BlockchainBlock extends ContentEntityBase implements BlockchainBlockInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['author'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Author of block'))
      ->setDescription(t('The user ID of author of the Blockchain Block entity.'));

    $fields['previous_hash'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Block previous hash'))
      ->setDescription(t('Hash of previous block.'));

    $fields['nonce'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Nonce of block'))
      ->setDescription(t('Nonce number for given block.'))
      ->setRequired(TRUE);

    $fields['data'] = BaseFieldDefinition::create('string_long')
      ->setSetting('case_sensitive', TRUE)
      ->setLabel(t('Block data'))
      ->setDescription(t('Serialized block data.'));

    $fields['timestamp'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->getTimestamp();
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    return $this->setTimestamp($timestamp);
  }

  /**
   * {@inheritdoc}
   */
  public function getTimestamp() {
    return $this->get('timestamp')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTimestamp($timestamp) {
    $this->set('timestamp', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getAuthor() {
    return $this->get('author')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setAuthor($author) {
    $this->set('author', $author);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    return $this->get('data')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setData($data) {
    $this->set('data', $data);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getNonce() {
    return $this->get('nonce')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setNonce($nonce) {
    $this->set('nonce', $nonce);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPreviousHash() {
    return $this->get('previous_hash')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setPreviousHash($hash) {
    $this->set('previous_hash', $hash);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getHash() {

    return Util::hash(
      $this->getData().
      $this->getTimestamp().
      $this->getNonce());
  }

}
