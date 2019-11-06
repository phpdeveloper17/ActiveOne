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

interface TransactionTypeInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    const ID = 'id';
    const TRANSACTION_NAME = 'transaction_name';
    const TENDER_TYPE = 'tender_type';
    const TAX_CLASS = 'tax_class';
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
    public function getTransactionName();
    /**
    * Set EmpId.
    */
    public function setTransactionName($transaction_name);

    
    public function getTenderType();

    public function setTenderType($tender_type);


    public function getTaxClass();

    public function setTaxClass($tax_class);
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
