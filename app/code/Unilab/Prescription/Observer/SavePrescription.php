<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Prescription\Observer;

use Magento\Framework\Event\ObserverInterface;

class SavePrescription implements ObserverInterface
{
	protected $quoteFactory;

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
	
	public function __construct(
		\Magento\Quote\Model\QuoteFactory $quoteFactory,
		\Magento\Sales\Api\OrderItemRepositoryInterface $orderItem
	)
	{
		$this->quoteFactory = $quoteFactory;
		$this->orderItem = $orderItem;
	}
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

		$order = $observer->getData('order');
		$quoteId = $order->getQuoteId();
		$quote = $this->quoteFactory->create()->load($quoteId);
		// $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/prescription.log');
		// $logger = new \Zend\Log\Logger();
		// $logger->addWriter($writer);
		foreach($quote->getAllItems() as $quoteItem) :
			$prescriptionId = $quoteItem->getPrescriptionId();
			if($prescriptionId) {
				foreach($order->getAllItems() as $orderItem) :
					if($orderItem->getProductId() == $quoteItem->getProductId()) :
						$item = $this->orderItem->get($orderItem->getItemId());
						$item->setPrescriptionId($prescriptionId);
						$item->save();
						//$logger->info('item id:'.$orderItem->getData('item_id'));
					endif;
				endforeach;
			}
		endforeach;

    }
}
