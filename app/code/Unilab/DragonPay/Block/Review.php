<?php
namespace Unilab\Dragonpayapi\Block;
class Review extends \Magento\Framework\View\Element\Template
{
	public function __construct(\Magento\Framework\View\Element\Template\Context $context)
	{
		parent::__construct($context);
	}
	public function getContinueUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
}
