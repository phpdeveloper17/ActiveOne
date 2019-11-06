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
class FromTimeFormat extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Renders grid column
     *
     * @param \Magento\Framework\DataObject $row
     * @return \Magento\Framework\Phrase|string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $time = $row->getData('limit_time_from');
        
        if (!$time) {
            return __('00:00:00');
        }
        $timeF = date('h:i A',strtotime($time));
        return $timeF;
    }
}