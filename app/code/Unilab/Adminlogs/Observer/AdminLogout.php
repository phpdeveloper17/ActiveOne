<?php
namespace Unilab\Adminlogs\Observer;

class AdminLogout implements \Magento\Framework\Event\ObserverInterface
{

	public function __construct(
        \Unilab\Adminlogs\Model\Logs $adminLogs

    ) {
        $this->adminLogs = $adminLogs;
	}
	
	public function execute(\Magento\Framework\Event\Observer $observer)
	{

		$this->adminLogs->createLogs('Logout','Success');

		return $this;
	}
}

		 

