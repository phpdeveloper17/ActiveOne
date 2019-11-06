<?php

namespace Unilab\Afptc\Block\Adminhtml\Afptc\Edit\Renderer;


use Magento\Framework\DataObject;

class Radio extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function render(DataObject $row)
    {
    
        $values = $this->getColumn()->getValues();
        
        $value  = $row->getData($this->getColumn()->getIndex());
       
        if (is_array($values)) {
            $checked = in_array($value, $values) ? ' checked="checked"' : '';
        } else {
            $checked = ($value === $this->getColumn()->getValue()) ? ' checked="checked"' : '';
        }
        $html = '<input onclick="awAfptcgridJsObjectName.reloadParams = {};'
                .'awAfptcgridJsObjectName.reloadParams.checkedValues = this.value" type="radio" name="'
                . $this->getColumn()->getHtmlName() . '" '
                . 'value="' . $row->getId() . '" class="radio"' . $checked . '/>'
        ;
        return $html;
    }
}