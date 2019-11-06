<?php
 
namespace Unilab\Checkout\Controller\Checkout;

use Magento\Framework\Controller\ResultFactory;
 

class Onepage extends \Magento\Framework\App\Action\Action
{
	protected $pageFactory;
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
		\Magento\Checkout\Model\Session $checkoutSession
		)
	{
		$this->pageFactory = $pageFactory;
		$this->checkoutSession = $checkoutSession;
		return parent::__construct($context);
	}

	public function execute()
	{
		$itemsCount = $this->checkoutSession->getQuote()->getItemsCount();
		
		if($itemsCount < 1) {
			$resultRedirect = $this->resultRedirectFactory->create();
			$resultRedirect->setPath('/');
			return $resultRedirect;
		}

		
		return $this->pageFactory->create();
	}
}