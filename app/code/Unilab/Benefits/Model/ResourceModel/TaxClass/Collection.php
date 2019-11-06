<?php
namespace Unilab\Benefits\Model\ResourceModel\TaxClass;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'class_id';
	// protected $_eventPrefix = 'unilab_benefits_TaxClasss_collection';
	// protected $_eventObject = 'TaxClasss_collection';

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
			'Unilab\Benefits\Model\TaxClass', 
			'Unilab\Benefits\Model\ResourceModel\TaxClass');
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

		$this->getSelect()->columns('(SELECT group_concat(class_name) FROM tax_class WHERE FIND_IN_SET(tax_class.class_id, main_table.tax_class)) AS tax_class_name');
	}

}