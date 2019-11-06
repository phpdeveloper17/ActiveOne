<?php
/**
 * Grid GridInterface.
 * @category  Unilab
 * @package   Unilab_Grid
 * @author    Unilab
 * @copyright Copyright (c) 2010-2017 Unilab Software Private Limited (https://Unilab.com)
 * @license   https://store.Unilab.com/license.html
 */

namespace Unilab\Benefits\Api\Data;

interface PurchaseCapInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    const ID = 'id';
    const PURCHASE_CAP_ID = 'purchase_cap_id';
    const PURCHASE_CAP_NAME = 'purchase_cap_des';
    const TNX_ID = 'tnx_id';
    const CREATED_AT = 'created_time';
    const UPDATED_AT = 'update_time';

    

    /**
    * Get Id.
    *
    * @return int
    */
    public function getId();

    public function setId($id);

    /**
    * Get EmployeeId.
    *
    * @return var
    */
    public function getPurchaseCapId();
    /**
    * Set EmpId.
    */
    public function setPurchaseCapId($param);

    
    public function getPurchaseCapName();

    public function setPurchaseCapName($param);

    public function getTnxId();

    public function setTnxId($param);
    /*
    * @return varchar
    */
    public function getUpdateAt();

   /**
    * Set UpdateTime.
    */
    public function setUpdateTime($param);

   /**
    * Get CreatedAt.
    *
    * @return varchar
    */
    public function getCreatedTime();

   /**
    * Set CreatedAt.
    */
    public function setCreatedTime($createdAt);
   
}
