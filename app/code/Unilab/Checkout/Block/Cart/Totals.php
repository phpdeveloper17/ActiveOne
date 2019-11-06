<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Checkout\Block\Cart;



class Totals extends \Magento\Framework\View\Element\Template

{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Directory\Model\Currency $currency,
        array $layoutProcessors = [],
        array $data = []
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->resource = $resource;
        $this->groupRepository = $groupRepository;
        $this->currency = $currency;

        parent::__construct($context, $data);
    }

    public function getQuote()
    {
        return $this->checkoutSession->getQuote();
    }

    public function getTotals()
    {   
        $quote = $this->getQuote();
        $items = $quote->getAllVisibleItems();
        $tax   = $this->getTaxClass();

        $vatableSale    = 0;
        $vatExempt      = 0;
        $vatZeroRate    = 0;
        $vatAmount      = 0;
		$shippingAmount	= 0;
        $taxClass       = $tax['tax_class'];
        $convenienceFee = $quote->getConveniencFee();
        
        if($taxClass == 'VAT Inclusive') :

            foreach($items as $item) :
                $taxId      = $item->getTaxClassId();
                $taxName    = $this->getProductTaxClass($taxId);

                if($taxName == 'Taxable Goods'):
                    $price       = $item->getRowTotal();
                    $taxAmount   = $price * ($tax['tax_rate'] /100);
                    $vatableSale += $price;
                    $vatAmount   += $taxAmount;
                else:
                    $vatExempt   += $item->getPrice() * $item->getQty();
                endif;

            endforeach;

        elseif($taxClass == 'VAT Exempt'):

            $vatExempt  = $quote->getSubtotal();

        elseif($taxClass == 'VAT Zero Rated'):

            $vatZeroRate = $quote->getSubtotal();

        endif;
		if($quote->getShippingAddress()->getShippingAmount()) {
			$shippingAmount = $quote->getShippingAddress()->getShippingAmount();
		}
		
		
		$grandTotal = $vatableSale + $vatExempt + $vatZeroRate + $vatAmount + $shippingAmount;

        $totals['tax_class']    = $taxClass;
        $totals['rate']         = $tax['tax_rate']; 
        $totals['order_amount'] = $quote->getSubtotal();
        $totals['vatable_sale'] = $vatableSale;    
        $totals['vat_exempt']   = $vatExempt;    
        $totals['vat_zeroRate'] = $vatZeroRate;    
        $totals['vat_amount']   = $vatAmount;    
        $totals['grand_total']  = $grandTotal;
        $totals['convenience']  = $convenienceFee;
        $totals['shipping']     = $quote->getShippingAddress();
        return $totals;
    }

    public function formatPrice($price)
    {
        return $this->currency->format($price, array('symbol' => '₱'), false, false);
    }
    private function getPaymentInfo()
    {
        $connection = $this->getConnection();
        $customerId = $this->customerSession->getCustomer()->getId();

        $sqlPurchaseCap 	= $connection->select()
                                            ->from('rra_emp_benefits', array('purchase_cap_id AS trans_type_id')) 
                                            ->where('entity_id=?', $customerId)
                                            ->where('is_active=?', 1); 

        $rowPurchase 		= $connection->fetchRow($sqlPurchaseCap);
        $pcapId				= $rowPurchase['trans_type_id'];

        $sqlTransactionType	= $connection->select()
                                        ->from('rra_emp_purchasecap', array('tnx_id AS transactiontype_id')) 
                                        ->where('id=?', $pcapId);
        $rowTransaction		= $connection->fetchRow($sqlTransactionType);			 
        $transtypeId		= $rowTransaction['transactiontype_id'];

        $sqlTenderType 		= $connection->select()
                                        ->from('rra_transaction_type', array('tender_type AS tendertype_id')) 
                                        ->where('id=?', $transtypeId);
        $rowTenderType		= $connection->fetchRow($sqlTenderType);
        $tenderTypeId		= $rowTenderType['tendertype_id'];

        $sqlPayment			= $connection->select()
                                        ->from('rra_tender_type', array('payment_method_code', 'tender_name')) 
                                        ->where('id=?', $tenderTypeId);
        $rowPayment			= $connection->fetchRow($sqlPayment);

        $methodCode 		= $rowPayment['payment_method_code'];
        $tenderName         = $rowPayment['tender_name'];

        return ['methodcode' => $methodCode, 'tender_name' => $tenderName];
    }

    private function getTaxClass()
    {
        $customerId = $this->customerSession->getCustomer()->getId();
        $groupId    = $this->customerSession->getCustomer()->getGroupId();

        $group      = $this->groupRepository->getById($groupId);

        $connection = $this->getConnection();

        $sqlTax     = $connection->select()->from('tax_class', array('class_name')) 
                                 ->where('class_id=?',$group->getTaxClassId()); 
        $taxClass   = $connection->fetchRow($sqlTax);

        $sqlTaxId   = $connection->select()->from('tax_calculation', array('tax_calculation_rate_id')) 
                                  ->where('customer_tax_class_id=?',$group->getTaxClassId());
        
        $taxRateId  = $connection->fetchRow($sqlTaxId);

        $sqlTaxRate = $connection->select()->from('tax_calculation_rate', array('rate')) 
                                           ->where('tax_calculation_rate_id=?',$taxRateId['tax_calculation_rate_id']);
        $taxRate    = $connection->fetchRow($sqlTaxRate);

        return [
            'tax_class' => $taxClass['class_name'], 
            'tax_rate' => $taxRate['rate']
        ];
    }

    private function getProductTaxClass($id) 
    {
        $connection = $this->getConnection();

        $sqlTax     = $connection->select()->from('tax_class', array('class_name')) 
                                ->where('class_id=?', $id);
        $taxClass   = $connection->fetchRow($sqlTax);

        return $taxClass['class_name'];
    }
    private function getConnection()
	{
		return $this->resource->getConnection();
	}

   
}
