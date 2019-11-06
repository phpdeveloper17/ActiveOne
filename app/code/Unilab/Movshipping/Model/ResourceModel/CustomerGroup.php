<?php
/**
 * Movshipping View XML.
 * @category  Unilab
 * @package   Unilab_Movshipping
 * @author    Kristian Claridad
 */
namespace Unilab\Movshipping\Model\ResourceModel;

class CustomerGroup extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var string
     */
    protected $_idFieldName = 'customer_group_id';
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
        $this->_init('customer_group', 'customer_group_id');
    }

    public function toOptionArray()
    {
        $connection = $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $customer_group_list = array(); // initialize array
        $select = $connection->select()->from(
            $connection->getTableName(
                'customer_group'
            )
        );
        $customer_group = $connection->fetchAll($select);
        foreach ($customer_group as $cm) {
           $customer_group_list[$cm['customer_group_id']] = array(
                'title'   => $cm['customer_group_code'],
                'value'   => $cm['customer_group_id'],
                'label'   => $cm['customer_group_code']
              );
        }

        return $customer_group_list;
    }

}
