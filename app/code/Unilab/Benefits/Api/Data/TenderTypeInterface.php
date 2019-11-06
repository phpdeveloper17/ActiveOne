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

interface TenderTypeInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    const ID = 'id';
    const TENDER_NAME = 'tender_name';
    const PAYMENT_METHOD = 'paymentmethod_code';
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
    public function getTenderName();
    /**
    * Set EmpId.
    */
    public function setTenderName($tender_name);

    
    public function getPaymentMethod();

    public function setPaymentMethod($payment_method);
    /*
    * @return varchar
    */
    public function getUpdateAt();

   /**
    * Set UpdateTime.
    */
    public function setUpdateTime($updateTime);

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
