<?php
 
namespace Unilab\Checkout\Controller\Adminhtml\Checkout;

use Magento\Framework\Controller\ResultFactory;
 

class Index extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory)
	{
		$this->_pageFactory = $pageFactory;
		return parent::__construct($context);
	}

	public function execute()
	{
		$resultPage = $this->pageFactory->create();
        // $resultPage->setActiveMenu('Unilab_Grid::grid_list');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Checkout'));
        return $resultPage;
	}
}