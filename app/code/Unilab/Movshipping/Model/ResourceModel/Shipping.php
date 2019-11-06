<?php
/**
 * Movshipping View XML.
 * @category  Unilab
 * @package   Unilab_Movshipping
 * @author    Kristian Claridad
 */
namespace Unilab\Movshipping\Model\ResourceModel;

class Shipping extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;
    private $resource;

    /**
     * Construct.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime       $date
     * @param string|null                                       $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\ResourceConnection $resource,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
        $this->resource = $resource;
    }

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('unilab_mov_shipping', 'id');
    }

    public function toOptionArray()
    {
        $connection = $this->resource->getConnection(
            \Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION
        );
        //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        //$city_collection = $objectManager->create('\Unilab\Movshipping\Model\ResourceModel\Shipping\Grid\Collection');
        $mov_shipping_list = array(); //initialize array
        
        $select = $connection->select()->from(
            $connection->getTableName(
                'unilab_mov_shipping'
            )
        );
        $mov_data = $connection->fetchAll($select);
        foreach ($mov_data as $m) {
           $mov_shipping_list[$m['id']] = array(
                'title'   => $m['group_name'],
                'value'   => $m['id'],
                'label'   => $m['group_name']
              );
        }
        return $mov_shipping_list;
    }

}
