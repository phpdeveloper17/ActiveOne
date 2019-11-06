<?php
/**
 * @category  Unilab
 * @package   Unilab_Reports
 * @author    Kristian Claridad
 */
namespace Unilab\Reports\Model\ResourceModel\Reports;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    const YOUR_TABLE = 'cleansql_report';

    protected $_idFieldName = 'report_id'; //this field id used to delete [id]=> of maintable[cleansql_report]

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
            'Unilab\Reports\Model\Reports',
            'Unilab\Reports\Model\ResourceModel\Reports'
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

        // // $this->getSelect()->joinLeft(
        // //         ['secondTable' => $this->getTable('unilab_cities')],
        // //         ' (FIND_IN_SET(secondTable.city_id, main_table.listofcities))',
        // //         ['name', 'group_concat(secondTable.name) as movcity']
        // //     );

        // // Add columns for movcity for listofcountries mysql query to select city name list base on id
        // $this->getSelect()->columns('(SELECT group_concat(name) FROM unilab_cities WHERE FIND_IN_SET(unilab_cities.city_id, main_table.listofcities)) AS movcity');
    }
}
?>