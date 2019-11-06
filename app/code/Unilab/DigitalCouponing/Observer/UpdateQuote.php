<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\DigitalCouponing\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class UpdateQuote implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Model\Session $customerSession,
        \Unilab\DigitalCouponing\Model\Update $update
        )
    {
        $this->logger = $logger;
        $this->customerSession = $customerSession;
        $this->update = $update;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    { 
        $item    = $observer->getQuoteItem();
        $dcInput = $this->customerSession->getDcInput();

        if($dcInput) :
            $this->update->updateQuote($item->getProductId(), $dcInput);
            $this->customerSession->unsDcInput();
        endif;
    }
}