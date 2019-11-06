<?php 

namespace Unilab\Benefits\Controller\Adminhtml\CompanyBranchController;

class Index extends \Magento\Backend\App\Action
{
	protected $resultPageFactory = false;
	protected $scopeConfig;
	protected $countryFactory;

	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Directory\Model\Region $countryFactory
	)
	{
		parent::__construct($context);
		$this->resultPageFactory = $resultPageFactory;
		$this->scopeConfig = $scopeConfig;
		$this->countryFactory = $countryFactory;
	}

	public function execute()
	{

		$resultPage = $this->resultPageFactory->create();
		$resultPage->getConfig()->getTitle()->prepend((__('Manage Company Branch Address')));

		return $resultPage;
	}


}