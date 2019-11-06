<?php

namespace Unilab\ConvenienceFee\Model\Invoice\Total;

use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

class Quote extends AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $invoice->setConveniencefee(0);
        
        $getConveniencefee = $invoice->getOrder()->getConveniencefee();
        $getBaseConveniencefee = $invoice->getOrder()->getBaseConveniencefee();
        $invoice->setConveniencefee($getConveniencefee);
        $invoice->setBaseConveniencefee($getBaseConveniencefee);

        $invoice->setGrandTotal($invoice->getGrandTotal() + $invoice->getConveniencefee());
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $invoice->getConveniencefee());

        return $this;
    }
}
