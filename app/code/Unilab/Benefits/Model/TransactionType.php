<?php
/**
 * Unilab_Grid Status Options Model.
 * @category    Unilab
 * @author      Unilab Software Private Limited
 */
namespace Unilab\Benefits\Model;

// use Magento\Framework\Data\OptionSourceInterface;
use Unilab\Benefits\Api\Data\TransactionTypeInterface;

class TransactionType extends \Magento\Framework\Model\AbstractModel implements TransactionTypeInterface
{
	const CACHE_TAG = 'rra_transaction_type';

	const TABLE_NAME = 'rra_transaction_type';

	protected $_cacheTag = 'rra_transaction_type';

	protected $_eventPrefix = 'rra_transaction_type';

	protected function _construct()
	{
		$this->_init('Unilab\Benefits\Model\ResourceModel\TransactionType');
	}

    public function getId() 
    {
    	return $this->getData(self::ID);
    }

    public function setID($id)
    {
    	return $this->setData(self::ID, $id);
    }

    public function getTransactionName()
    {
    	return $this->getData(self::TRANSACTION_NAME);
    }

    public function setTransactionName($param)
    {
    	return $this->setData(self::TRANSACTION_NAME, $param);
    }

    public function getTenderType()
    {
    	return $this->getData(self::TENDER_TYPE);
    }

    public function setTenderType($param)
    {
    	return $this->setData(self::TENDER_TYPE, $param);
    }

    public function getTaxClass()
    {
        return $this->getData(self::TAX_CLASS);
    }

    public function setTaxClass($param)
    {
        return $this->setData(self::TAX_CLASS, $param);
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

