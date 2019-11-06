<?php 

namespace Unilab\Benefits\Controller\Adminhtml\EmployeeBenefitsController;

class Index extends \Magento\Backend\App\Action
{
	protected $resultPageFactory = false;
	protected $backendSession;

	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Session $backendSession,
		\Unilab\Benefits\Cron\Autorefreshperiod $autorefresh
	)
	{
		parent::__construct($context);
		$this->resultPageFactory = $resultPageFactory;
		$this->backendSession = $backendSession;
		$this->_autorefresh = $autorefresh;
	}

	public function execute()
	{
		$this->backendSession->getData('create_benefit', true);
		$resultPage = $this->resultPageFactory->create();
		$resultPage->getConfig()->getTitle()->prepend((__('Manage Employee Benefits')));
		
		try{
			//$autoref = $this->_autorefresh->refreshBenefits();
			//$this->messageManager->addSuccess(__('Successfully Refresh Period.'));
		}catch(\Exception $e){
			$this->messageManager->addError(__($e->getMessage()));
		}
		return $resultPage;
	}


}