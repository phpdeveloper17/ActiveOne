<?php

namespace Unilab\Afptc\Block\Adminhtml\Afptc\Edit\Renderer;


use Magento\Framework\DataObject;

class Status extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function render(DataObject $row)
    {
    
        $value  = $row->getData($this->getColumn()->getIndex());
        if($value == 1){
            return 'Enabled';
        }else{
            return 'Disabled';
        }
    }
}