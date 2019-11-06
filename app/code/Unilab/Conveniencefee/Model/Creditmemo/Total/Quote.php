<?php

namespace Unilab\ConvenienceFee\Model\Creditmemo\Total;

use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

class Quote extends AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $creditmemo->setConveniencefee(0);
        
        $getConveniencefee = $creditmemo->getOrder()->getConveniencefee();
        $getBaseConveniencefee = $creditmemo->getOrder()->getBaseConveniencefee();

        $creditmemo->setConveniencefee($getConveniencefee);
        $creditmemo->setBaseConveniencefee($getBaseConveniencefee);

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $creditmemo->getConveniencefee());
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $creditmemo->getConveniencefee());

        return $this;
    }
}
