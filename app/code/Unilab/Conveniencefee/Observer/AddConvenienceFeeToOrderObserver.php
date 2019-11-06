<?php
namespace Unilab\Conveniencefee\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AddConvenienceFeeToOrderObserver implements ObserverInterface
{
    protected $_objectManager;
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectmanager)
    {
        $this->_objectManager = $objectmanager;
    }
    /**
     * Set payment fee to order
     *
     * @param EventObserver $observer
     * @return $this
     */
    // save to sales_order table
    public function execute(EventObserver $observer)
    {
        $order = $observer->getOrder();
        $quoteRepository = $this->_objectManager->create('Magento\Quote\Model\QuoteRepository');
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $quoteRepository->get($order->getQuoteId());
      
        $order->setData('conveniencefee', $quote->getConveniencefee());
        $order->setData('base_conveniencefee', $quote->getBaseConveniencefee());

        return $this;
    }
}
