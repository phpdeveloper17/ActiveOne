<?php
/**
 * Unilab_Grid Status Options Model.
 * @category    Unilab
 * @author      Unilab Software Private Limited
 */
namespace Unilab\Benefits\Model;

// use Magento\Framework\Data\OptionSourceInterface;
use Unilab\Benefits\Api\Data\PurchasecapInterface;

class Purchasecap extends \Magento\Framework\Model\AbstractModel implements PurchaseCapInterface
{
    const CACHE_TAG = 'rra_emp_purchasecap';

    const TABLE_NAME = 'rra_emp_purchasecap';

    protected $_cacheTag = 'rra_emp_purchasecap';

    protected $_eventPrefix = 'rra_emp_purchasecap';

    protected function _construct()
    {
        $this->_init('Unilab\Benefits\Model\ResourceModel\Purchasecap');
    }

    public function getId() 
    {
        return $this->getData(self::ID);
    }

    public function setID($id)
    {
        return $this->setData(self::ID, $id);
    }

    public function getPurchaseCapId()
    {
        return $this->getData(self::PURCHASE_CAP_ID);
    }

    public function setPurchaseCapId($param)
    {
        return $this->setData(self::PURCHASE_CAP_ID, $param);
    }

    public function getPurchaseCapName()
    {
        return $this->getData(self::PURCHASE_CAP_NAME);
    }

    public function setPurchaseCapName($param)
    {
        return $this->setData(self::PURCHASE_CAP_NAME, $param);
    }

    public function getTnxId()
    {
        return $this->getData(self::TNX_ID);
    }

    public function setTnxId($param)
    {
        return $this->setData(self::TNX_ID, $param);
    }

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

    public function getUpdateAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

   /**
    * Set CreatedAt.
    */
    public function setUpdateTime($createdAt)
    {
        return $this->setData(self::UPDATED_AT, $createdAt);
    }
}

