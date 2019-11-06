<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\DigitalCouponing\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SaveCoupon implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function __construct(
        \Magento\Sales\Model\Order $orderFactory,
        \Unilab\DigitalCouponing\Model\Update $update
    )
    {
        $this->orderFactory = $orderFactory;
        $this->updateOrder  = $update;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        // $incrementId = $order->getIncrementId();

        // $order = $this->orderFactory->load($incrementId);

        $this->updateOrder->saveUsedCoupon($order);
    }
}
