<?php

namespace Drupal\blockchain\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Blockchain config entity.
 *
 * @ConfigEntityType(
 *   id = "blockchain_config",
 *   label = @Translation("Blockchain config"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\blockchain\BlockchainConfigListBuilder",
 *     "form" = {
 *       "edit" = "Drupal\blockchain\Form\BlockchainConfigForm",
 *       "delete" = "Drupal\blockchain\Form\BlockchainConfigDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\blockchain\BlockchainConfigHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "blockchain_config",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/blockchain_config/{blockchain_config}",
 *     "edit-form" = "/admin/structure/blockchain_config/{blockchain_config}/edit",
 *     "delete-form" = "/admin/structure/blockchain_config/{blockchain_config}/delete",
 *     "collection" = "/admin/structure/blockchain_config"
 *   }
 * )
 */
class BlockchainConfig extends ConfigEntityBase implements BlockchainConfigInterface {

  /**
   * The Blockchain config ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Blockchain config label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Blockchain id.
   *
   * @var string
   */
  protected $blockchainId;

  /**
   * The Blockchain node id.
   *
   * @var string
   */
  protected $nodeId;

  /**
   * The Blockchain type.
   *
   * @var string
   */
  protected $type;

  protected $isAuth;

  protected $filterType;

  protected $filterList;

  protected $poolManagement;

  protected $announceManagement;

  protected $intervalPool;

  protected $intervalAnnounce;

  protected $powPosition;

  protected $powExpression;

  protected $dataHandler;

  protected $allowNotSecure;

  /**
   * @return mixed
   */
  public function getBlockchainId() {

    return $this->blockchainId;
  }

  /**
   * @param mixed $blockchainId
   * @return BlockchainConfig
   */
  public function setBlockchainId($blockchainId)
  {
    $this->blockchainId = $blockchainId;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getNodeId()
  {
    return $this->nodeId;
  }

  /**
   * @param mixed $nodeId
   * @return BlockchainConfig
   */
  public function setNodeId($nodeId)
  {
    $this->nodeId = $nodeId;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getType()
  {
    return $this->type;
  }

  /**
   * @param mixed $type
   * @return BlockchainConfig
   */
  public function setType($type)
  {
    $this->type = $type;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getIsAuth()
  {
    return $this->isAuth;
  }

  /**
   * @param mixed $isAuth
   * @return BlockchainConfig
   */
  public function setIsAuth($isAuth)
  {
    $this->isAuth = $isAuth;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getFilterType()
  {
    return $this->filterType;
  }

  /**
   * @param mixed $filterType
   * @return BlockchainConfig
   */
  public function setFilterType($filterType)
  {
    $this->filterType = $filterType;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getFilterList()
  {
    return $this->filterList;
  }

  /**
   * @param mixed $filterList
   * @return BlockchainConfig
   */
  public function setFilterList($filterList)
  {
    $this->filterList = $filterList;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getPoolManagement()
  {
    return $this->poolManagement;
  }

  /**
   * @param mixed $poolManagement
   * @return BlockchainConfig
   */
  public function setPoolManagement($poolManagement)
  {
    $this->poolManagement = $poolManagement;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getAnnounceManagement()
  {
    return $this->announceManagement;
  }

  /**
   * @param mixed $announceManagement
   * @return BlockchainConfig
   */
  public function setAnnounceManagement($announceManagement)
  {
    $this->announceManagement = $announceManagement;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getIntervalPool()
  {
    return $this->intervalPool;
  }

  /**
   * @param mixed $intervalPool
   * @return BlockchainConfig
   */
  public function setIntervalPool($intervalPool)
  {
    $this->intervalPool = $intervalPool;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getIntervalAnnounce()
  {
    return $this->intervalAnnounce;
  }

  /**
   * @param mixed $intervalAnnounce
   * @return BlockchainConfig
   */
  public function setIntervalAnnounce($intervalAnnounce)
  {
    $this->intervalAnnounce = $intervalAnnounce;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getPowPosition()
  {
    return $this->powPosition;
  }

  /**
   * @param mixed $powPosition
   * @return BlockchainConfig
   */
  public function setPowPosition($powPosition)
  {
    $this->powPosition = $powPosition;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getPowExpression()
  {
    return $this->powExpression;
  }

  /**
   * @param mixed $powExpression
   * @return BlockchainConfig
   */
  public function setPowExpression($powExpression)
  {
    $this->powExpression = $powExpression;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getDataHandler()
  {
    return $this->dataHandler;
  }

  /**
   * @param mixed $dataHandler
   * @return BlockchainConfig
   */
  public function setDataHandler($dataHandler)
  {
    $this->dataHandler = $dataHandler;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getAllowNotSecure()
  {
    return $this->allowNotSecure;
  }

  /**
   * @param mixed $allowNotSecure
   * @return BlockchainConfig
   */
  public function setAllowNotSecure($allowNotSecure)
  {
    $this->allowNotSecure = $allowNotSecure;
    return $this;
  }

  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param string $id
   * @return BlockchainConfig
   */
  public function setId($id)
  {
    $this->id = $id;
    return $this;
  }

  /**
   * @return string
   */
  public function getLabel()
  {
    return $this->label;
  }

  /**
   * @param string $label
   * @return BlockchainConfig
   */
  public function setLabel($label)
  {
    $this->label = $label;
    return $this;
  }

}
