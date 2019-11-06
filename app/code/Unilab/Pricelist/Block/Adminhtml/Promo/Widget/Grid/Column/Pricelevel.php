<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Pricelist\Block\Adminhtml\Promo\Widget\Grid\Column;

/**
 * Grid column block that is displayed only in multistore mode
 *
 * @api
 * @deprecated 100.2.0 in favour of UI component implementation
 * @since 100.0.2
 */
class Pricelevel extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
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
        $id = $row->getData('price_level_id');
        
        if (!$id) {
            return __('');
        }
        $pl = $this->_objectManager->create("Unilab\Benefits\Model\Pricelevel")->load($id);
        $pld_id = $pl->getData('price_level_id');
        // echo "<pre>";
        //     print_r($pl->getData('price_level_id'));
        // echo "</pre>";
        return $pld_id;
    }
}