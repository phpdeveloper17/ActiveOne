<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Unilab\Pricelist\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveAndContinueButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     * @codeCoverageIgnore
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->canRender('save_and_continue_edit')) {
            $data = [
                'label' => __('Save and Continue Edit'),
                'class' => 'save primary',
                'on_click' => '',
                'sort_order' => 4,
            ];
        }
        return $data;
    }
}
