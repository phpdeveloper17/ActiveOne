<?php
namespace Unilab\Afptc\Block\Adminhtml\Afptc\Edit\Renderer;

use Magento\Backend\Block\Template;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class Products extends Template implements RendererInterface
{
    protected function _construct()
    {
        $this->setTemplate('products.phtml');
    }

    public function render(AbstractElement $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }
    
    public function getGridHtml()
    {         
        return $this->getLayout()->createBlock('Unilab\Afptc\Block\Adminhtml\Afptc\Edit\Grid\Products')
                ->setElement($this->getElement())
                ->toHtml();
    } 
}