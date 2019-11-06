<?php

namespace Unilab\Customers\Model;


class CustomerGroups extends \Magento\Framework\Model\AbstractModel 
{

    const CACHE_TAG = 'customer_group';

    protected $_cacheTag = 'customer_group';


    protected $_eventPrefix = 'customer_group';


    protected function _construct()
    {
        $this->_init('Unilab\Customers\Model\ResourceModel\CustomerGroups');
    }

}
