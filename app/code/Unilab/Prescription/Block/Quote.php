<?php

namespace Unilab\Prescription\Block;

class Quote extends \Magento\Framework\View\Element\Template
{

    protected $checkoutSession;
    
	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = []
		)
	{
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context, $data);
	}

    public function getQuote()
    {
        return $this->checkoutSession->getQuote()->getId();
    }

	
}