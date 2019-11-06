<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Checkout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class UpdatePurchaseCap implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function __construct(
        \Unilab\Checkout\Model\Purchasecap $purchaseCap
    )
    {
        $this->purchaseCap  = $purchaseCap;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    { 
        $order = $observer->getEvent()->getOrder();

        $this->purchaseCap->update($order);
    }
}