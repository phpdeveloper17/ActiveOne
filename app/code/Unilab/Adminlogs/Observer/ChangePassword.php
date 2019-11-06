<?php
namespace Unilab\Adminlogs\Observer;

class ChangePassword implements \Magento\Framework\Event\ObserverInterface
{

	public function __construct(
        \Unilab\Adminlogs\Model\Logs $adminLogs

    ) {
        $this->adminLogs = $adminLogs;
	}
	
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$this->adminLogs->createLogs('Change Password','Success');
	
		return $this;
	}
}

		 

