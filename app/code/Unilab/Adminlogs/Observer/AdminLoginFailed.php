<?php
namespace Unilab\Adminlogs\Observer;

class AdminLoginFailed implements \Magento\Framework\Event\ObserverInterface
{

	public function __construct(
        \Unilab\Adminlogs\Model\Logs $adminLogs

    ) {
        $this->adminLogs = $adminLogs;
	}
	
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
        $username = $observer->getUserName();

		$this->adminLogs->createLogs('Login','Failed', $username);

		return $this;
	}
}

		 

