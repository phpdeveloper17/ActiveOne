<?php

namespace Unilab\Prescription\Model\ResourceModel\Prescription;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */

    protected $_idFieldName = 'prescription_id';
    
    protected function _construct()
    {
        $this->_init(
            'Unilab\Prescription\Model\Prescription',
            'Unilab\Prescription\Model\ResourceModel\Prescription'
        );
    }
}