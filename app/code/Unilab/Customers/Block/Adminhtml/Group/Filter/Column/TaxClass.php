<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Customers\Block\Adminhtml\Group\Filter\Column;

class TaxClass extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Select
{
    protected function _getOptions()
    {
        $emptyOption = ['value' => null, 'label' => ''];

        $optionGroups = $this->getColumn()->getOptionGroups();
        if ($optionGroups) {
            array_unshift($optionGroups, $emptyOption);
            return $optionGroups;
        }

        // $colOptions = $this->getColumn()->getOptions();
        // $colOptions = [0=>'Clear', 1=>'Hold'];
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $colOptions = $this->_objectManager->create("\Magento\Tax\Model\TaxClass\Source\Customer")->toOptionArray();
        if (!empty($colOptions) && is_array($colOptions)) {
            $options = [$emptyOption];

            foreach ($colOptions as $key => $option) {
                if (is_array($option)) {
                    $options[] = $option;
                } else {
                    $options[] = ['value' => $key, 'label' => $option];
                }
            }
            return $options;
        }
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getHtml()
    {
        $html = '<select name="' . $this->_getHtmlName() . '" id="' . $this->_getHtmlId() . '"' . $this->getUiId(
                'filter',
                $this->_getHtmlName()
            ) . 'class="no-changes admin__control-select">';
        $value = $this->getValue();
        foreach ($this->_getOptions() as $option) {
            if (is_array($option['value'])) {
                $html .= '<optgroup label="' . $this->escapeHtml($option['label']) . '">';
                foreach ($option['value'] as $subOption) {
                    $html .= $this->_renderOption($subOption, $value);
                }
                $html .= '</optgroup>';
            } else {
                $html .= $this->_renderOption($option, $value);
            }
        }
        $html .= '</select>';
        return $html;
    }
}