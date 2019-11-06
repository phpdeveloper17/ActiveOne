<?php
namespace Unilab\Checkout\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class SetCustomPriceInCart implements ObserverInterface
{

	public function execute(\Magento\Framework\Event\Observer $observer) 
	{
            $quote_item = $observer->getEvent()->getQuoteItem();
            $price = $observer->getProduct()->getPrice(); //set your price here
            $quote_item->setCustomPrice($price);
            $quote_item->setOriginalCustomPrice($price);
            $quote_item->getProduct()->setIsSuperMode(true);
    }
}