<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Adminhtml customers group page content block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Unilab\Customers\Block\Adminhtml;

/**
 * @api
 * @since 100.0.2
 */
class Group extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Modify header & button labels
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'customer_group';
        $this->_headerText = __('Customer Groups');
        $this->_addButtonLabel = __('Add New Customer Group');
        $this->buttonList->add(
            'import',
            [
                'label' => __('Import Customer Group'),
                'onclick' => "location.href='" . $this->getUrl('unilab_customers/*/import') . "'",
                'class' => 'primary'
            ],
            10
        );
        parent::_construct();
    }

    /**
     * Redefine header css class
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'icon-head head-customer-groups';
    }
}
