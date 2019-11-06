<?php
/**
 * @category  Unilab
 * @package   Unilab_Adminlogs
 * @author    Kristian Claridad
 */
namespace Unilab\Adminlogs\Model\ResourceModel\Adminlogs;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    const YOUR_TABLE = 'unilab_adminlogs';

    protected $_idFieldName = 'id'; //this field id used to delete [id]=> of maintable[unilab_adminlogs]

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
            'Unilab\Adminlogs\Model\Adminlogs',
            'Unilab\Adminlogs\Model\ResourceModel\Adminlogs'
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
}
?>