<?php
/**
 * Grid GridInterface.
 * @category  Unilab
 * @package   Unilab_Grid
 * @author    Unilab
 * @copyright Copyright (c) 2010-2017 Unilab Software Private Limited (https://Unilab.com)
 * @license   https://store.Unilab.com/license.html
 */

namespace Unilab\Inquiry\Api\Data;

interface InquiryInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    const INQUIRY_ID = 'inquiry_id';
    const STORE_ID = 'store_id';
    const CUSTOMER_ID = 'customer_id';
    const DEPARTMENT_EMAIL = 'department_email';
    const CONCERN = 'concern';
    const DEPARTMENT = 'department';
    const EMAIL_ADDRESS = 'email_address';
    const NAME = 'name';
    const IS_READ =  'is_read';
    const CREATED_TIME = 'created_time';

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
    public function getStoreId();
    /**
    * Set EmpId.
    */
    public function setStoreId($param);

    /**
    * Get GroupId.
    *
    * @return int
    */
    public function getCustomerId();

    /**
    * Set GroupId.
    */
    public function setCustomerId($param);
   /**
    * Get EntityId.
    *
    * @return int
    */
    public function getDepartmentEmail();

   /**
    * Set EntityId.
    */
    public function setDepartmentEmail($param);

    /**
    * Get EmployeeName.
    *
    * @return var
    */
    public function getConcern();

    /**
    * Set EmpName.
    */
    public function setConcern($name);

    /**
    * Get Purchase Cap Limit.
    *
    * @return var
    */
    public function getDepartment();

    /**
    * Set Purchase Cap Limit.
    */
    public function setDepartment($param);

    /**
    * Get PurchaseCapId.
    *
    * @return var
    */
    public function getEmailAddress();

    /**
    * Set PurchaseCapId.
    */
    public function setEmailAddress($param);

    /**
    * Get Cosumed.
    *
    * @return var
    */
    public function getName();

    /**
    * Set Cosumed.
    */
    public function setName($consumed);

    /**
    * Get Available.
    *
    * @return var
    */
    public function getIsRead();

    /**
    * Set Available.
    */
    public function setIsRead($param);

    /**
    * Get Extension.
    *
    * @return var
    */
    public function getCreatedTime();

    /**
    * Set Extension.
    */
    public function setCreatedTime($param);

}
