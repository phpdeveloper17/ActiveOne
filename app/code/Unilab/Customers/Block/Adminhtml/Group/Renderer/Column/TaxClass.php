<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Customers\Block\Adminhtml\Group\Renderer\Column;

/**
 * Grid column block that is displayed only in multistore mode
 *
 * @api
 * @deprecated 100.2.0 in favour of UI component implementation
 * @since 100.0.2
 */
class TaxClass extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Renders grid column
     *
     * @param \Magento\Framework\DataObject $row
     * @return \Magento\Framework\Phrase|string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $id = $row->getData('tax_class_id');
        
        if (!$id) {
            return __('');
        }
        $pl = $this->_objectManager->create("Magento\Tax\Api\Data\TaxClassInterface")->load($id);
        $pld_id = $pl->getClassName();
     
        return $pld_id;
    }
    
}