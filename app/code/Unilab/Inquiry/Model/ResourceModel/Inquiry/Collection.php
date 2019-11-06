<?php
namespace Unilab\Inquiry\Model\ResourceModel\Inquiry;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'inquiry_id';
	// protected $_eventPrefix = 'unilab_benefits_employeebenefits_collection';
	// protected $_eventObject = 'employeebenefits_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init(
			'Unilab\Inquiry\Model\Inquiry',
			'Unilab\Inquiry\Model\ResourceModel\Inquiry');
	}

	public function _initSelect()
	{
		parent::_initSelect();

		$this->getSelect()->columns('(SELECT group_concat(name) FROM store WHERE FIND_IN_SET(store.store_id, main_table.store_id)) AS store_name');
		$this->getSelect()->columns('(SELECT group_concat(CONCAT(firstname, " ", lastname)) FROM customer_entity WHERE FIND_IN_SET(customer_entity.entity_id, main_table.customer_id)) AS customer_name');
	}

}
