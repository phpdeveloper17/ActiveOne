<?php
namespace Unilab\Benefits\Model\ResourceModel\TransactionType;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'id';
	// protected $_eventPrefix = 'unilab_benefits_TransactionTypes_collection';
	// protected $_eventObject = 'TransactionTypes_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	public function __construct(
		\Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
	) 
	{
		$this->_init(
			'Unilab\Benefits\Model\TransactionType', 
			'Unilab\Benefits\Model\ResourceModel\TransactionType');
		parent::__construct(
            $entityFactory, $logger, $fetchStrategy, $eventManager, $connection,
            $resource
        );
        $this->storeManager = $storeManager;
	}

	protected function _construct()
	{
		
	}

	public function _initSelect() 
	{
		parent::_initSelect();

		$this->getSelect()->joinLeft(
            ['secondTable' => $this->getTable('rra_tender_type')],
            'FIND_IN_SET(secondTable.id,main_table.tender_type)',
            ['tender_name' => "group_concat(secondTable.tender_name)"]
		)->group('main_table.id');
		$this->addFilterToMap('tender_name', 'secondTable.tender_name');
		$this->addFilterToMap('id', 'main_table.id');
		$this->addFilterToMap('created_time', 'main_table.created_time');
		$this->addFilterToMap('update_time', 'main_table.update_time');
		// $this->getSelect()->columns('(SELECT group_concat(tender_name) FROM rra_tender_type WHERE FIND_IN_SET(rra_tender_type.id, main_table.tender_type)) AS tender_name');
		
		$this->getSelect()->columns('(SELECT group_concat(class_name) FROM tax_class WHERE FIND_IN_SET(tax_class.class_id, main_table.tax_class)) AS tax_class_name');

	}

}