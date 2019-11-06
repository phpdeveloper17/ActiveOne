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

interface EmployeeBenefitInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    const ID = 'id';
    const EMPLOYEE_ID = 'emp_id';
    const GROUP_ID = 'group_id';
    const ENTITY_ID = 'entity_id';
    const EMPLOYEE_NAME = 'emp_name';
    const PURCHASE_CAP_LIMIT = 'purchase_cap_limit';
    const PURCHASE_CAP_ID = 'purchase_cap_id';
    const CONSUMED = 'consumed';
    const AVAILABLE = 'available';
    const EXTENSION = 'extension';
    const REFRESH_PERIOD = 'refresh_period';
    const START_DATE = 'start_date';
    const REFRESH_DATE = 'refresh_date';
    const IS_ACTIVE = 'is_active';
    const CREATED_AT = 'created_time';
    const UPDATED_AT = 'update_time';
    const UPLOADED_BY = 'uploadedby';
    const DATE_UPLOADED = 'date_uploaded';

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
    public function getEmpId();
    /**
    * Set EmpId.
    */
    public function setEmpId($emp_id);

    /**
    * Get GroupId.
    *
    * @return int
    */
    public function getGroupId();

    /**
    * Set GroupId.
    */
    public function setGroupId($group_id);
   /**
    * Get EntityId.
    *
    * @return int
    */
    public function getEntityId();

   /**
    * Set EntityId.
    */
    public function setEntityId($entityId);

    /**
    * Get EmployeeName.
    *
    * @return var
    */
    public function getEmpName();

    /**
    * Set EmpName.
    */
    public function setEmpName($name);

    /**
    * Get Purchase Cap Limit.
    *
    * @return var
    */
    public function getPurchaseCapLimit();

    /**
    * Set Purchase Cap Limit.
    */
    public function setPurchaseCapLimit($purchase_cap_limit);

    /**
    * Get PurchaseCapId.
    *
    * @return var
    */
    public function getPurchaseCapId();

    /**
    * Set PurchaseCapId.
    */
    public function setPurchaseCapId($purchase_cap_id);

    /**
    * Get Cosumed.
    *
    * @return var
    */
    public function getConsumed();

    /**
    * Set Cosumed.
    */
    public function setConsumed($consumed);

    /**
    * Get Available.
    *
    * @return var
    */
    public function getAvaible();

    /**
    * Set Available.
    */
    public function setAvaible($available);

    /**
    * Get Extension.
    *
    * @return var
    */
    public function getExtension();

    /**
    * Set Extension.
    */
    public function setExtension($extension);

    /**
    * Get RefreshPeriod.
    *
    * @return var
    */
    public function getRefreshPeriod();

    /**
    * Set RefreshPeriod.
    */
    public function setRefreshPeriod($refresh_period);

    /**
    * Get StartDate.
    *
    * @return var
    */
    public function getStartDate();

    /**
    * Set StartDate.
    */
    public function setStartDate($start_date);

    /**
    * Get RefreshDate.
    *
    * @return var
    */
    public function getRefreshDate();

    /**
    * Set RefreshDate.
    */
    public function setRefreshDate($refresh_date);

   /**
    * Get IsActive.
    *
    * @return varchar
    */
    public function getIsActive();

   /**
    * Set StartingPrice.
    */
    public function setIsActive($isActive);

   /**
    * Get UpdateTime.
    *
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

    /**
    * Get UploadedBy.
    *
    * @return var
    */
    public function getUploadedBy();

    /**
    * Set UploadedBy.
    */
    public function setUploadedBy($uploader);

    /**
    * Get DateUploaded.
    *
    * @return var
    */
    public function getDateUploaded();

    /**
    * Set DateUploaded.
    */
    public function setDateUploaded($date_uploaded);
}
