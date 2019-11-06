<?php

namespace Unilab\BpiSecurepay\Observer;

class BpiSecurepayObserver implements \Magento\Framework\Event\ObserverInterface
{
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$result = $observer->getData('order');
		return $result->getText();
	}
}