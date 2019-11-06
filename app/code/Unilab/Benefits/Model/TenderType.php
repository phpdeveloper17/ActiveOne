<?php
/**
 * Unilab_Grid Status Options Model.
 * @category    Unilab
 * @author      Unilab Software Private Limited
 */
namespace Unilab\Benefits\Model;

// use Magento\Framework\Data\OptionSourceInterface;
use Unilab\Benefits\Api\Data\TenderTypeInterface;

class TenderType extends \Magento\Framework\Model\AbstractModel implements TenderTypeInterface
{
	const CACHE_TAG = 'rra_tender_type';

	const TABLE_NAME = 'rra_tender_type';

	protected $_cacheTag = 'rra_tender_type';

	protected $_eventPrefix = 'rra_tender_type';

	protected function _construct()
	{
		$this->_init('Unilab\Benefits\Model\ResourceModel\TenderType');
	}

    public function getId() 
    {
    	return $this->getData(self::ID);
    }

    public function setID($id)
    {
    	return $this->setData(self::ID, $id);
    }

    public function getTenderName()
    {
    	return $this->getData(self::TENDER_NAME);
    }

    public function setTenderName($param)
    {
    	return $this->setData(self::TENDER_NAME, $param);
    }

    public function getPaymentMethod()
    {
    	return $this->getData(self::PAYMENT_METHOD);
    }

    public function setPaymentMethod($param)
    {
    	return $this->setData(self::PAYMENT_METHOD, $param);
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

