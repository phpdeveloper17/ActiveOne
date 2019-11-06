<?php

/**
 * Unilab
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Unilab.com license that is
 * available through the world-wide-web at this URL:
 * http://www.Unilab.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Unilab
 * @package     Unilab_Customers
 */

namespace Unilab\Customers\Ui\Component\Listing\Group\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\App\ResourceConnection;
use Magento\Tax\Api\Data\TaxClassInterface;
class TaxClass extends Column
{

    protected $connection;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        ResourceConnection $resource,
        TaxClassInterface $taxClass,
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->_resource = $resource;
        $this->_taxClass = $taxClass;
    }

    public function prepareDataSource(array $dataSource)
    {
       
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$items) {
                $taxClassId = $items['tax_class_id'];
                $taxClassName = $this->_taxClass->load($taxClassId);
                $items['tax_class_id'] = $taxClassName->getClassName();
            }
        }
        return $dataSource;
    }
    
}
