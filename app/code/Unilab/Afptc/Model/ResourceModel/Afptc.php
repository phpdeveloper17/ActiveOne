<?php
/**
 * Afptc View XML.
 * @category  Unilab
 * @package   Unilab_Afptc
 * @author    Kristian Claridad
 */
namespace Unilab\Afptc\Model\ResourceModel;

class Afptc extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var string
     */
    protected $_idFieldName = 'rule_id';
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
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('aw_afptc_rules', 'rule_id');
    }
    public function getActiveRulesCollection($store)
    {
        $rulesCollection = $this->_objectManager->create("\Unilab\Afptc\Model\Afptc")->getCollection();
        $rulesCollection
            ->addStatusFilter()
            ->addTimeLimitFilter()
            ->addStoreFilter((int) $store->getId())
            ->addGroupFilter((int) $this->_objectManager->create("\Unilab\Afptc\Helper\Data")->getCustomerGroup())
            ->addPriorityOrder()
        ;
        
        return $rulesCollection;
    }
}
