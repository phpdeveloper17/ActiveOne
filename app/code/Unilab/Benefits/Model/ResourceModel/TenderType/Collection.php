<?php
namespace Unilab\Benefits\Model\ResourceModel\TenderType;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'id';
	// protected $_eventPrefix = 'unilab_benefits_TenderTypes_collection';
	// protected $_eventObject = 'TenderTypes_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init(
			'Unilab\Benefits\Model\TenderType', 
			'Unilab\Benefits\Model\ResourceModel\TenderType');
	}

}