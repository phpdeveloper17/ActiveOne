<?php
/**
 * Reports View XML.
 * @category  Unilab
 * @package   Unilab_Reports
 * @author    Kristian Claridad
 */
namespace Unilab\Reports\Model;

// use Unilab\Reports\Api\Data\ReportsInterface;

class Reports extends \Magento\Framework\Model\AbstractModel 
{

    const CACHE_TAG = 'cleansql_report';

    protected $_cacheTag = 'cleansql_report';


    protected $_eventPrefix = 'cleansql_report';


    protected function _construct()
    {
        $this->_init('Unilab\Reports\Model\ResourceModel\Reports');
    }


}
