<?php

/**
 * Grid Grid Collection.
 *
 * @category  Unilab
 * @package   Unilab_Grid
 * @author    Unilab
 * @copyright Copyright (c) 2010-2017 Unilab Software Private Limited (https://Unilab.com)
 * @license   https://store.Unilab.com/license.html
 */
namespace Unilab\DigitalCouponing\Model\ResourceModel\Grid;

class RemainderCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';
    /**
     * Define resource model.
     */
    protected function _construct()
    {
        $this->_init(
            'Unilab\Remainder\Model\Remainder',
            'Unilab\Remainder\Model\ResourceModel\Remainder'
        );
    }
}
