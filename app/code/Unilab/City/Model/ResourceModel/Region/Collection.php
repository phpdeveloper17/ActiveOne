<?php
/**
 * City View XML.
 * @category  Unilab
 * @package   Unilab_City
 * @author    Ron Mark Peroso Rudas   
 */
namespace Unilab\City\Model\ResourceModel\Region;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'region_id';

    protected function _construct()
    {
        $this->_init(
            'Unilab\City\Model\Region',
            'Unilab\City\Model\ResourceModel\Region'
        );
    }
}
