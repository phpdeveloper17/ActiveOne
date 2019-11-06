<?php
namespace Unilab\Benefits\Model;

use Unilab\Benefits\Api\Data\CompanyBranchInterface;
// class CompanyBranch extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
class CompanyBranch extends \Magento\Framework\Model\AbstractModel implements CompanyBranchInterface
{
	const CACHE_TAG = 'rra_company_branches';
    const TABLE_NAME_VALUE = 'rra_company_branches';
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

	protected $_cacheTag = 'rra_company_branches';

	protected $_eventPrefix = 'rra_company_branches';

	protected $_resourceConnection;

    // public function __construct(
    //     \Magento\Framework\App\ResourceConnection $resourceConnection,
    //     \Magento\Framework\Session\SessionManagerInterface $coreSession,
    //     \Magento\Framework\App\Filesystem\DirectoryList $directorylist,
    //     \Psr\Log\LoggerInterface $logger
    // ) {
    //     $this->_resourceConnection = $resourceConnection;
    //     $this->_coreSession = $coreSession;
    //     $this->_directorylist = $directorylist;
    //     $this->_logger = $logger;
        
    // } 
    protected function _construct()
	{
        $this->_init('Unilab\Benefits\Model\ResourceModel\CompanyBranch');
        // $this->_isgetcompanyID("800000012");
        
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
    public function getCompanyId()
    {
        return $this->getData(self::COMPANY_ID);
    }
    /**
    * Set EmpId.
    */
    public function setCompanyId($emp_id)
    {
        return $this->setData(self::COMPANY_ID, $emp_id);
    }

    /**
    * Get GroupId.
    *
    * @return int
    */
    public function getContactPerson()
    {
        return $this->getData(self::CONTACT_PERSON);
    }

    /**
    * Set GroupId.
    */
    public function setContactPerson($group_id)
    {
        return $this->setData(self::CONTACT_PERSON, $group_id);
    }
   /**
    * Get EntityId.
    *
    * @return int
    */
    public function getContactNumber()
    {
        return $this->getData(self::CONTACT_NUMBER);
    }

   /**
    * Set EntityId.
    */
    public function setContactNumber($entityId)
    {
        return $this->setData(self::CONTACT_NUMBER, $entityId);
    }

    /**
    * Get EmployeeName.
    *
    * @return var
    */
    public function getBranchAddress()
    {
        return $this->getData(self::BRANCH_ADDRESS);
    }

    /**
    * Set EmpName.
    */
    public function setBranchAddress($name)
    {
        return $this->setData(self::BRANCH_ADDRESS, $name);
    }

    /**
    * Get Purchase Cap Limit.
    *
    * @return var
    */
    public function getBranchProvince()
    {
        return $this->getData(self::BRANCH_PROVINCE);
    }

    /**
    * Set Purchase Cap Limit.
    */
    public function setBranchProvince($purchase_cap_limit)
    {
        return $this->setData(self::BRANCH_PROVINCE, $purchase_cap_limit);
    }

    /**
    * Get PurchaseCapId.
    *
    * @return var
    */
    public function getBranchCity()
    {
        return $this->getData(self::BRANCH_CITY);
    }

    /**
    * Set PurchaseCapId.
    */
    public function setBranchCity($purchase_cap_id)
    {
        return $this->setData(self::BRANCH_CITY, $purchase_cap_id);
    }

    /**
    * Get Cosumed.
    *
    * @return var
    */
    public function getBranchPostcode()
    {
        return $this->getData(self::BRANCH_POSTCODE);
    }

    /**
    * Set Cosumed.
    */
    public function setBranchPostcode($consumed)
    {
        return $this->setData(self::BRANCH_POSTCODE, $consumed);
    }

    /**
    * Get Available.
    *
    * @return var
    */
    public function getShippingAddress()
    {
        return $this->getData(self::SHIPPING_ADDRESS);
    }

    /**
    * Set Available.
    */
    public function setShippingAddress($available)
    {
        return $this->setData(self::SHIPPING_ADDRESS, $available);
    }

    /**
    * Get Extension.
    *
    * @return var
    */
    public function getBillingAddress()
    {
        return $this->getData(self::BILLING_ADDRESS);
    }

    /**
    * Set Extension.
    */
    public function setBillingAddress($extension)
    {
        return $this->setData(self::BILLING_ADDRESS, $extension);
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
    public function getShipCode()
    {
        return $this->getData(self::SHIP_CODE);
    }

    /**
    * Set UploadedBy.
    */
    public function setShipCode($uploader)
    {
        return $this->setData(self::SHIP_CODE, $uploader);
    }

}