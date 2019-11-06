<?php
namespace Unilab\Healthcredits\Block;
class Failed extends \Magento\Framework\View\Element\Template
{
	protected $_template = 'Unilab_Healthcredits::failed.phtml';
	public function __construct(\Magento\Framework\View\Element\Template\Context $context)
	{
		parent::__construct($context);
	}
	public function getContinueUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
}
