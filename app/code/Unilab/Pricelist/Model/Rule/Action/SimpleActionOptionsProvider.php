<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Pricelist\Model\Rule\Action;

class SimpleActionOptionsProvider implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('By Percentage of the Original Price'),
                'value' => 'by_percent'
            ],
            [
                'label' => __('By Fixed Amount'),
                'value' => 'by_fixed'
            ],
            [
                'label' => __('To Percentage of the Original Price'),
                'value' => 'to_percent'
            ],
            [
                'label' => __('To Fixed Amount'),
                'value' => 'to_fixed'
            ]
        ];
    }
}
