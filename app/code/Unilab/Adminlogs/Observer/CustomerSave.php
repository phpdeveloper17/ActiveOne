<?php
namespace Unilab\Adminlogs\Observer;

class CustomerSave implements \Magento\Framework\Event\ObserverInterface
{

	public function __construct(
        \Unilab\Adminlogs\Model\Logs $adminLogs

    ) {
        $this->adminLogs = $adminLogs;
	}
	
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$action = '';
		$customer  = $observer->getCustomer();
		
		if($customer->getCreatedAt() == $customer->getUpdatedAt()) {
			$action = 'Customer Add';
		}
		else {
			$action = 'Customer Edit';
		}
		$this->adminLogs->createLogs($action, 'Success');
	
		return $this;
	}
}

		 

