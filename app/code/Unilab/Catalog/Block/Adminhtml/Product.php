<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Catalog manage products block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Unilab\Catalog\Block\Adminhtml;

/**
 * @api
 * @since 100.0.2
 */
class Product extends \Magento\Catalog\Block\Adminhtml\Product
{
    
    protected function _prepareLayout()
    {
        
        $this->buttonList->add(
            'import',
            [
                'label' => __('Import Product'),
                'onclick' => "setLocation('" . $this->getUrl('unilab_catalog/*/import') . "')",
                'class' => 'primary'
            ],
            10
        );

        return parent::_prepareLayout();
    }

  
}
