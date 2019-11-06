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

interface CompanyBranchInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    const ID = 'id';
    const COMPANY_ID = 'company_id';
    const CONTACT_PERSON = 'contact_person';
    const CONTACT_NUMBER = 'contact_number';
    const BRANCH_ADDRESS = 'branch_address';
    const BRANCH_PROVINCE = 'branch_province';
    const BRANCH_CITY = 'branch_city';
    const BRANCH_POSTCODE = 'branch_postcode';
    const SHIPPING_ADDRESS = 'shipping_address';
    const BILLING_ADDRESS = 'billing_address';
    const CREATED_AT = 'created_time';
    const UPDATED_AT = 'update_time';
    const SHIP_CODE = 'ship_code';
    

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
    public function getCompanyId();
    /**
    * Set EmpId.
    */
    public function setCompanyId($emp_id);

    /**
    * Get GroupId.
    *
    * @return int
    */
    public function getContactPerson();

    /**
    * Set GroupId.
    */
    public function setContactPerson($group_id);
   /**
    * Get EntityId.
    *
    * @return int
    */
    public function getContactNumber();

   /**
    * Set EntityId.
    */
    public function setContactNumber($entityId);

    /**
    * Get EmployeeName.
    *
    * @return var
    */
    public function getBranchAddress();

    /**
    * Set EmpName.
    */
    public function setBranchAddress($name);

    /**
    * Get Purchase Cap Limit.
    *
    * @return var
    */
    public function getBranchProvince();

    /**
    * Set Purchase Cap Limit.
    */
    public function setBranchProvince($purchase_cap_limit);

    /**
    * Get PurchaseCapId.
    *
    * @return var
    */
    public function getBranchCity();

    /**
    * Set PurchaseCapId.
    */
    public function setBranchCity($purchase_cap_id);

    /**
    * Get Cosumed.
    *
    * @return var
    */
    public function getBranchPostcode();

    /**
    * Set Cosumed.
    */
    public function setBranchPostcode($consumed);

    /**
    * Get Available.
    *
    * @return var
    */
    public function getShippingAddress();

    /**
    * Set Available.
    */
    public function setShippingAddress($available);

    /**
    * Get Extension.
    *
    * @return var
    */
    public function getBillingAddress();

    /**
    * Set Extension.
    */
    public function setBillingAddress($extension);

    /**
    * Get RefreshPeriod.
    *
    * @return var
    */
    public function getShipCode();

    /**
    * Set RefreshPeriod.
    */
    public function setShipCode($refresh_period);

   
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
