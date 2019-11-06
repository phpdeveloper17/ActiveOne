<?php
/**
 * @category  Unilab
 * @package   Unilab_Pricelist
 * @author    Kristian Claridad
 */
namespace Unilab\Pricelist\Model\ResourceModel\Pricelist;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    const YOUR_TABLE = 'wspi_pricelist';

    protected $_idFieldName = 'id'; //this field id used to delete [id]=> of maintable[wspi_pricelist]

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
            'Unilab\Pricelist\Model\Pricelist',
            'Unilab\Pricelist\Model\ResourceModel\Pricelist'
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

        $this->getSelect()->joinLeft(
            ['secondTable' => $this->getTable('catalogrule')],
            ' main_table.id = secondTable.rule_id',
            ['secondTable.name as catalog_name','secondTable.from_qty as from_qty','secondTable.to_qty as to_qty']
        )->joinLeft(
            ['thirdTable' => $this->getTable('rra_pricelevelmaster')],
            ' main_table.price_level_id = thirdTable.price_level_id',
            ['thirdTable.price_level_id as rra_pricelevel_id']
        );
        
    }
}
?>