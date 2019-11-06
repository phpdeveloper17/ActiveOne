<?php
/**
 * Reports View XML.
 * @category  Unilab
 * @package   Unilab_Reports
 * @author    Kristian Claridad
 */
namespace Unilab\Reports\Model;

// use Unilab\Reports\Api\Data\ReportsInterface;

class Sales extends \Magento\Framework\Model\AbstractModel 
{

    const CACHE_TAG = 'sales_order';

    protected $_cacheTag = 'sales_order';


    protected $_eventPrefix = 'sales_order';


    protected function _construct()
    {
        // $this->_init('Unilab\Reports\Model\ResourceModel\Sales');
    }


}
