<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Conveniencefee\Model\Total\Convenience;

class Subtotal extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    protected $quoteValidator = null; 
    protected $helperData;
    protected $_customerSession;

    public function __construct(
        \Magento\Quote\Model\QuoteValidator $quoteValidator,
        \Unilab\Conveniencefee\Helper\Data $helperData,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->quoteValidator = $quoteValidator;
        $this->helperData = $helperData;
        $this->_customerSession = $customerSession;
    }
     public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $address
    )
    {
        parent::collect($quote, $shippingAssignment, $address);
        $subtotal       = 0;
        $baseSubtotal   = 0;
        $subtotalInclTax= 0;
        $baseSubtotalInclTax = 0;

        $order = $invoice->getOrder();

        foreach ($invoice->getAllItems() as $item) {
            if ($item->getOrderItem()->isDummy()) {
                continue;
            }

            $item->calcRowTotal();

            $subtotal       += $item->getRowTotal();
            $baseSubtotal   += $item->getBaseRowTotal();
            $subtotalInclTax+= $item->getRowTotalInclTax();
            $baseSubtotalInclTax += $item->getBaseRowTotalInclTax();
        }

        $allowedSubtotal = $order->getSubtotal() - $order->getSubtotalInvoiced();
        $baseAllowedSubtotal = $order->getBaseSubtotal() - $order->getBaseSubtotalInvoiced();
        $allowedSubtotalInclTax = $allowedSubtotal + $order->getHiddenTaxAmount()
                + $order->getTaxAmount() - $order->getTaxInvoiced() - $order->getHiddenTaxInvoiced();
        $baseAllowedSubtotalInclTax = $baseAllowedSubtotal + $order->getBaseHiddenTaxAmount()
                + $order->getBaseTaxAmount() - $order->getBaseTaxInvoiced() - $order->getBaseHiddenTaxInvoiced();

        /**
         * Check if shipping tax calculation is included to current invoice.
         */
        $includeShippingTax = true;
        foreach ($invoice->getOrder()->getInvoiceCollection() as $previousInvoice) {
            if ($previousInvoice->getShippingAmount() && !$previousInvoice->isCanceled()) {
                $includeShippingTax = false;
                break;
            }
        }

        if ($includeShippingTax) {
            $allowedSubtotalInclTax     -= $order->getShippingTaxAmount();
            $baseAllowedSubtotalInclTax -= $order->getBaseShippingTaxAmount();
        } else {
            $allowedSubtotalInclTax     += $order->getShippingHiddenTaxAmount();
            $baseAllowedSubtotalInclTax += $order->getBaseShippingHiddenTaxAmount();
        }

        if ($invoice->isLast()) {
            $subtotal = $allowedSubtotal;
            $baseSubtotal = $baseAllowedSubtotal;
            $subtotalInclTax = $allowedSubtotalInclTax;
            $baseSubtotalInclTax  = $baseAllowedSubtotalInclTax;
        } else {
            $subtotal = min($allowedSubtotal, $subtotal);
            $baseSubtotal = min($baseAllowedSubtotal, $baseSubtotal);
            $subtotalInclTax = min($allowedSubtotalInclTax, $subtotalInclTax);
            $baseSubtotalInclTax = min($baseAllowedSubtotalInclTax, $baseSubtotalInclTax);
        }

		$convenienceFeeExclTax = 0;
		$convenienceFeeInclTax = 0;
		
		if($order->getPayment()->getMethod() == "dragonpay"){
			$convenienceFeeExclTax = $order->getConvenienceFee();
			$convenienceFeeInclTax = $this->helperData->getConveniencefeeValue();
		}
		
        $invoice->setSubtotal($subtotal);
        $invoice->setBaseSubtotal($baseSubtotal);
        $invoice->setSubtotalInclTax($subtotalInclTax);
        $invoice->setBaseSubtotalInclTax($baseSubtotalInclTax);

        $invoice->setGrandTotal($invoice->getGrandTotal() + $subtotal  + $convenienceFeeExclTax);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseSubtotal  + $convenienceFeeInclTax);
		
        return $this;
    }
}
