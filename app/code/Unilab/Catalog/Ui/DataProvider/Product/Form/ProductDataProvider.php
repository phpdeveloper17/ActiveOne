<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Catalog\Ui\DataProvider\Product\Form;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

/**
 * DataProvider for product edit form
 *
 * @api
 * @since 101.0.0
 */
class ProductDataProvider extends \Magento\Catalog\Ui\DataProvider\Product\Form\ProductDataProvider
{
    
    public function getMeta()
    {
        $meta = parent::getMeta();
        unset($meta['product-details']['children']['container_visibility']['children']['visibility']);
        return $meta;
    }
}
