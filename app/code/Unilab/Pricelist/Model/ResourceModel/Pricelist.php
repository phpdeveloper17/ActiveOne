<?php
/**
 * Pricelist View XML.
 * @category  Unilab
 * @package   Unilab_Pricelist
 * @author    Kristian Claridad
 */
namespace Unilab\Pricelist\Model\ResourceModel;

class Pricelist extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
        $this->_init('wspi_pricelist', 'id');
    }
    
}
