<?php
/**
 * @category  Unilab
 * @package   Unilab_Pricelevel
 * @author    Kristian Claridad
 */
namespace Unilab\Pricelist\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    const YOUR_TABLE = 'catalogrule';

    protected $_idFieldName = 'rule_id'; //this field id used to delete [id]=> of maintable[wspi_pricelist]

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
            'Unilab\Pricelist\Model\CatalogRule',
            'Unilab\Pricelist\Model\ResourceModel\CatalogRule'
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
        // $this->addWebsitesToResult();

        $this->getSelect()
            ->joinLeft(
                ['secondTable' => $this->getTable('catalogrule_customer_group')],
                'main_table.rule_id = secondTable.rule_id',
                [''])
            ->joinLeft(
                ['customer_group_tbl' => $this->getTable('customer_group')],
                'secondTable.customer_group_id = customer_group_tbl.customer_group_id',
                ['GROUP_CONCAT(`customer_group_tbl`.`customer_group_code`) AS customer_group_code','GROUP_CONCAT(`customer_group_tbl`.`customer_group_id`) AS customergroupid'])
            ->joinLeft(
                ['rra_pricelevelmaster_tbl' => $this->getTable('rra_pricelevelmaster')],
                'main_table.price_level_id = rra_pricelevelmaster_tbl.id',
                ['rra_pricelevelmaster_tbl.price_level_id as price_level']
            )->group('main_table.rule_id');
        $this->addFilterToMap('price_level', 'rra_pricelevelmaster_tbl.price_level_id');
        $this->addFilterToMap('is_active', 'main_table.is_active');
        $this->addFilterToMap('rule_id', 'main_table.rule_id');
        
        return $this;
    }
}
?>