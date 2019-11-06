<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Catalog price rules
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Unilab\Pricelist\Block\Adminhtml\Promo;

/**
 * @api
 * @since 100.0.2
 */
class Catalog extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    
    protected function _construct()
    {
        $this->_blockGroup = 'Unilab_Pricelist';
        $this->_controller = 'adminhtml_promo_catalog';
        $this->_headerText = __('Manage Price List');
        $this->_addButtonLabel = __('Add Price List');
        parent::_construct();

        $this->buttonList->add(
            'import',
            [
                'label' => __('Import Price List'),
                'onclick' => "setLocation('" . $this->getUrl('pricelist/*/import') . "')",
                'class' => 'primary'
            ]
        );

        // $this->buttonList->add(
        //     'apply_rules',
        //     [
        //         'label' => __('Apply Rules'),
        //         'onclick' => "location.href='" . $this->getUrl('pricelist/*/applyRules') . "'",
        //         'class' => 'apply'
        //     ]
        // );
        
    }
}
