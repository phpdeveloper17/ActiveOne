<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Customers\Block\Adminhtml\Group\Filter\Column;

class Storeviews extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Select
{
    private $_systemStore;
    protected $_escaper;
    protected $currentOptions = [];

    public function __construct(
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Framework\Escaper $_escaper
    ) {
        $this->_systemStore = $systemStore;
        $this->_escaper=$_escaper;
    }
    protected function _getOptions()
    {
        
       
    }

    /**
     * {@inheritdoc}
     */
    public function getHtml()
    {
        $html = '<select name="' . $this->_getHtmlName() . '" id="' . $this->_getHtmlId() . '"' . $this->getUiId(
            'filter',
            $this->_getHtmlName()
        ) . 'class="no-changes admin__control-select" style="width:150px;">';
        $html .= '<option value>All Store Views</option>';
        $websiteCollection = $this->_systemStore->getWebsiteCollection();
        $groupCollection = $this->_systemStore->getGroupCollection();
        $storeCollection = $this->_systemStore->getStoreCollection();
        /** @var \Magento\Store\Model\Website $website */
        foreach ($websiteCollection as $website) {
            
            $groups = [];
            /** @var \Magento\Store\Model\Group $group */
            foreach ($groupCollection as $group) {
                if ($group->getWebsiteId() == $website->getId()) {
                    $stores = [];
                    /** @var  \Magento\Store\Model\Store $store */
                    foreach ($storeCollection as $store) {
                        if ($store->getGroupId() == $group->getId()) {
                            $name = $this->_escaper->escapeHtml($store->getName());
                            $stores[$name]['label'] = str_repeat('&nbsp;', 3). $name;
                            $stores[$name]['value'] = $store->getId();
                        }
                    }
                    if (!empty($stores)) {
                        $name = $this->_escaper->escapeHtml($group->getName());
                        $groups[$name]['label'] = $name;
                        $groups[$name]['value'] = array_values($stores);
                    }
                }
            }
            
            if (!empty($groups)) {
                $name = $this->_escaper->escapeHtml($website->getName());
                $this->currentOptions[$name]['label'] = $name;
                $this->currentOptions[$name]['value'] = array_values($groups);
            }
        }
        $value = $this->getValue();
        foreach ($this->currentOptions as $option) {
            if (is_array($option['value'])) {
                $html .= '<optgroup label="' . $this->_escaper->escapeHtml($option['label']) . '">';
                foreach ($option['value'] as $subOptionGroup) {
                    $html .= '<optgroup label="' . str_repeat('&nbsp;', 3) .$this->_escaper->escapeHtml($subOptionGroup['label']) . '">';
                    foreach ($subOptionGroup['value'] as $subOption) {
                        $html .= $this->_renderOption($subOption, $value);
                    }
                }
                $html .= '</optgroup>';
            } else {
                $html .= $this->_renderOption($option, $value);
            }
        }
        // die;
        $html .= '</select>';
        return $html;
    }
}