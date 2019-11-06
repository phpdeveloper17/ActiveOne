<?php
/**
 * @category  Unilab
 * @package   Unilab_Benefits->Pricelevel
 * @author    Kristian Claridad
 */
namespace Unilab\Benefits\Api\Data;

interface PricelevelInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    
    const ID = 'id';
    const PRICE_NAME = 'price_name';
    const PRICE_LEVEL_ID = 'price_level_id';
    const IS_ACTIVE = 'is_active';
    const MEMO = 'memo';
    const PREFIX = 'prefix';
    const CREATED_TIME = 'created_time';
    const UPDATE_TIME = 'update_time';
   
   /**
    * Get EntityId.
    *
    * @return int
    */
    public function getId();
    public function setId($id);

    public function getPriceName();
    public function setPriceName($price_name);

    public function getPriceLevelID();
    public function setPriceLevelID($price_level_id);

    public function getIsActive();
    public function setIsActive($is_active);

    public function getMemo();
    public function setMemo($memo);

    public function getPrefix();
    public function setPrefix($prefix);

    public function getCreatedTime();
    public function setCreatedTime($created_time);

    public function getUpdateTime();
    public function setUpdateTime($update_time);

}
