<?php
/**
 * @category  Unilab
 * @package   Unilab_Afptc
 * @author    Kristian Claridad
 */
namespace Unilab\Afptc\Model\ResourceModel\Afptc;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    const YOUR_TABLE = 'aw_afptc_rules';

    protected $_idFieldName = 'rule_id'; //this field id used to delete [rule_id]=> of maintable[aw_afptc_rules]

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->_init(
            'Unilab\Afptc\Model\Afptc',
            'Unilab\Afptc\Model\ResourceModel\Afptc'
        );
        parent::__construct(
            $entityFactory, $logger, $fetchStrategy, $eventManager, $connection,
            $resource
        );
        $this->storeManager = $storeManager;
        
    }
    protected function _initSelect()
    {
        parent::_initSelect();

    }
    
    public function addStatusFilter()
    {
        $this->getSelect()->where('main_table.status = ?', 1);
        return $this;
    }

    public function addPriorityOrder()
    {
        $this->getSelect()->order('main_table.priority DESC');
        return $this;
    }
    
    public function addTimeLimitFilter()
    {
        $this->getSelect()
            ->where("if(main_table.end_date is null, true, main_table.end_date > UTC_TIMESTAMP()) AND
                if(main_table.start_date is null, true, main_table.start_date < UTC_TIMESTAMP())");

        return $this;
    }
    
    public function addStoreFilter($store)
    {
        $this->getSelect()->where('find_in_set(0, store_ids) OR find_in_set(?, store_ids)', $store);
        return $this;
    }
    
    public function addGroupFilter($group)
    {
        $this->getSelect()->where('find_in_set(?, customer_groups)', $group);
        return $this;
    }
}
?>