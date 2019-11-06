<?php
namespace Unilab\Benefits\Model\ResourceModel\Purchasecap;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'id';
	// protected $_eventPrefix = 'unilab_benefits_Purchasecaps_collection';
	// protected $_eventObject = 'Purchasecaps_collection';

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
			'Unilab\Benefits\Model\Purchasecap', 
			'Unilab\Benefits\Model\ResourceModel\Purchasecap');
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
            ['secondTable' => $this->getTable('rra_transaction_type')],
            'FIND_IN_SET(secondTable.id,main_table.tnx_id)',
            ['transaction_name' => "group_concat(secondTable.transaction_name)"]
		)->group('main_table.id');
		$this->addFilterToMap('transaction_name', 'secondTable.transaction_name');
		$this->addFilterToMap('created_time', 'main_table.created_time');
		$this->addFilterToMap('update_time', 'main_table.update_time');
		$this->addFilterToMap('id', 'main_table.id');

	}

}