<?php
namespace Unilab\Benefits\Model;

use Unilab\Benefits\Api\Data\EmployeeBenefitInterface;

// class EmployeeBenefit extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
class EmployeeBenefit extends \Magento\Framework\Model\AbstractModel implements EmployeeBenefitInterface
{
	const CACHE_TAG = 'rra_emp_benefits';
    // const ID = 'id';
    // const EMPLOYEE_ID = 'emp_id';
    // const GROUP_ID = 'group_id';
    // const ENTITY_ID = 'entity_id';
    // const EMPLOYEE_NAME = 'emp_name';
    // const PURCHASE_CAP_LIMIT = 'purchase_cap_limit';
    // const PURCHASE_CAP_ID = 'purchase_cap_id';
    // const CONSUMED = 'consumed';
    // const AVAILABLE = 'available';
    // const EXTENSION = 'extension';
    // const REFRESH_PERIOD = 'refresh_period';
    // const START_DATE = 'start_date';
    // const REFRESH_DATE = 'refresh_date';
    // const IS_ACTIVE = 'is_active';
    // const CREATED_AT = 'created_time';
    // const UPDATE_AT = 'update_time';
    // const UPLOADED_BY = 'uploadedby';
    // const DATE_UPLOADED = 'date_uploaded';

	protected $_cacheTag = 'rra_emp_benefits';

	protected $_eventPrefix = 'rra_emp_benefits';

	protected function _construct()
	{
		$this->_init('Unilab\Benefits\Model\ResourceModel\EmployeeBenefit');
	}

    // public function getIdentities()
    // {
    //     return [self::CACHE_TAG . '_' . $this->getId()];
    // }
	/**
    * Get Id.
    *
    * @return int
    */
    public function getId() 
    {
        return $this->getData(self::ID);
    }

    public function setId($id) 
    {
        return $this->setData(self::ID, $id);
    }

    /**
    * Get EmployeeId.
    *
    * @return var
    */
    public function getEmpId()
    {
        return $this->getData(self::EMPLOYEE_ID);
    }
    /**
    * Set EmpId.
    */
    public function setEmpId($emp_id)
    {
        return $this->setData(self::EMPLOYEE_ID, $emp_id);
    }

    /**
    * Get GroupId.
    *
    * @return int
    */
    public function getGroupId()
    {
        return $this->getData(self::GROUP_ID);
    }

    /**
    * Set GroupId.
    */
    public function setGroupId($group_id)
    {
        return $this->setData(self::GROUP_ID, $group_id);
    }
   /**
    * Get EntityId.
    *
    * @return int
    */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

   /**
    * Set EntityId.
    */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
    * Get EmployeeName.
    *
    * @return var
    */
    public function getEmpName()
    {
        return $this->getData(self::EMPLOYEE_NAME);
    }

    /**
    * Set EmpName.
    */
    public function setEmpName($name)
    {
        return $this->setData(self::EMPLOYEE_NAME, $name);
    }

    /**
    * Get Purchase Cap Limit.
    *
    * @return var
    */
    public function getPurchaseCapLimit()
    {
        return $this->getData(self::PURCHASE_CAP_LIMIT);
    }

    /**
    * Set Purchase Cap Limit.
    */
    public function setPurchaseCapLimit($purchase_cap_limit)
    {
        return $this->setData(self::PURCHASE_CAP_LIMIT, $purchase_cap_limit);
    }

    /**
    * Get PurchaseCapId.
    *
    * @return var
    */
    public function getPurchaseCapId()
    {
        return $this->getData(self::PURCHASE_CAP_ID);
    }

    /**
    * Set PurchaseCapId.
    */
    public function setPurchaseCapId($purchase_cap_id)
    {
        return $this->setData(self::PURCHASE_CAP_ID, $purchase_cap_id);
    }

    /**
    * Get Cosumed.
    *
    * @return var
    */
    public function getConsumed()
    {
        return $this->getData(self::CONSUMED);
    }

    /**
    * Set Cosumed.
    */
    public function setConsumed($consumed)
    {
        return $this->setData(self::CONSUMED, $consumed);
    }

    /**
    * Get Available.
    *
    * @return var
    */
    public function getAvaible()
    {
        return $this->getData(self::AVAILABLE);
    }

    /**
    * Set Available.
    */
    public function setAvaible($available)
    {
        return $this->setData(self::AVAILABLE, $available);
    }

    /**
    * Get Extension.
    *
    * @return var
    */
    public function getExtension()
    {
        return $this->getData(self::EXTENSION);
    }

    /**
    * Set Extension.
    */
    public function setExtension($extension)
    {
        return $this->setData(self::EXTENSION, $extension);
    }

    /**
    * Get RefreshPeriod.
    *
    * @return var
    */
    public function getRefreshPeriod()
    {
        return $this->getData(self::REFRESH_PERIOD);
    }

    /**
    * Set RefreshPeriod.
    */
    public function setRefreshPeriod($refresh_period)
    {
        return $this->setData(self::REFRESH_PERIOD, $refresh_period);
    }

    /**
    * Get StartDate.
    *
    * @return var
    */
    public function getStartDate()
    {
        return $this->getData(self::START_DATE);
    }

    /**
    * Set StartDate.
    */
    public function setStartDate($start_date)
    {
        return $this->setData(self::START_DATE, $start_date);
    }

    /**
    * Get RefreshDate.
    *
    * @return var
    */
    public function getRefreshDate()
    {
        return $this->getData(self::REFRESH_DATE);
    }

    /**
    * Set RefreshDate.
    */
    public function setRefreshDate($refresh_date)
    {
        return $this->setData(self::REFRESH_DATE, $refresh_date);
    }

   /**
    * Get IsActive.
    *
    * @return varchar
    */
    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

   /**
    * Set StartingPrice.
    */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

   /**
    * Get UpdateTime.
    *
    * @return varchar
    */
    public function getUpdateAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

   /**
    * Set UpdateTime.
    */
    public function setUpdateTime($updateTime)
    {
        return $this->setData(self::UPDATED_AT, $updateTime);
    }

   /**
    * Get CreatedAt.
    *
    * @return varchar
    */
    public function getCreatedTime()
    {
        return $this->getData(self::CREATED_AT);
    }

   /**
    * Set CreatedAt.
    */
    public function setCreatedTime($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
    * Get UploadedBy.
    *
    * @return var
    */
    public function getUploadedBy()
    {
        return $this->getData(self::UPLOADED_BY);
    }

    /**
    * Set UploadedBy.
    */
    public function setUploadedBy($uploader)
    {
        return $this->setData(self::UPLOADED_BY, $uploader);
    }

    /**
    * Get DateUploaded.
    *
    * @return var
    */
    public function getDateUploaded()
    {
        return $this->getData(self::DATE_UPLOADED);
    }

    /**
    * Set DateUploaded.
    */
    public function setDateUploaded($date_uploaded)
    {
        return $this->setData(self::DATE_UPLOADED, $date_uploaded);
    }
}