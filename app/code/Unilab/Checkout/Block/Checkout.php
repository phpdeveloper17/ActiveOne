<?php

namespace Unilab\Checkout\Block;

class Checkout extends \Magento\Framework\View\Element\Template
{

    const CHECKOUT_CONFIG = 'ulcheckout';
    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
        private $amountValid = true;
    
        protected $paymentMethods = [
            'offline-payments' => [
                'code' => 'offline-payments',
                'component' => 'Magento_OfflinePayments/js/view/payment/method-renderer/offline-payments'
            ],
            'cashondelivery' => [
                'code' => 'cashondelivery',
                'component' => 'Unilab_Checkout/js/payments/cashondelivery'
            ],
            'bpisecurepay' => [
                'code' => 'bpisecurepay',
                'component' => 'Unilab_BpiSecurepay/js/view/payment/method-renderer/bpisecurepay'
            ],
            'dragonpay' => [
                'code' => 'dragonpay',
                'component' => 'Unilab_DragonPay/js/view/payment/method-renderer/dragonpay'
            ],
            'healthcredits' => [
                'code' => 'healthcredits',
                'component' => 'Unilab_Healthcredits/js/view/payment/method-renderer/healthcredits'
            ]
        
        ];

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Shipping\Model\Config $shipConfig,
        \Magento\Checkout\Model\CompositeConfigProvider $configProvider,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Directory\Model\Currency $currency,
        array $layoutProcessors = [],
        array $data = []
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->scopeConfig = $scopeConfig;
        $this->shipConfig = $shipConfig;
        $this->customerSession = $customerSession;
        $this->customer = $customer;
        $this->configProvider = $configProvider;
        $this->layoutProcessors = $layoutProcessors;
        $this->resource = $resource;
        $this->groupRepository = $groupRepository;
        $this->currency = $currency;

        $this->jsLayout = isset($data['jsLayout']) && is_array($data['jsLayout']) ? $data['jsLayout'] : [];
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
        ->get(\Magento\Framework\Serialize\Serializer\Json::class);
        parent::__construct($context, $data);
    }

    /**
     * Get form action URL for POST booking request
     *
     * @return string
     */
    public function getFormAction()
    {
    }

    public function getJsLayout()
    {
        foreach ($this->layoutProcessors as $processor) {
            $this->jsLayout = $processor->process($this->jsLayout);
        }
        return $this->serializer->serialize($this->jsLayout);
    }

    public function getQuoteData()
    {
        $this->checkoutSession->getQuote();
        if (!$this->hasData('quote')) {
            $this->setData('quote', $this->checkoutSession->getQuote());
        }
        return $this->getData('quote');
    }

    public function getCheckoutSession()
    {
        return $this->checkoutSession;
    }
    public function getQuote()
    {
        return $this->getCheckoutSession()->getQuote();
    }

    public function getCustomerAddresses()
    {
        if($this->customerSession->isLoggedIn()) {
            
            $customer = $this->customer->load($this->customerSession->getCustomer()->getId());
            
            $addresses = [];

            $defaultBillingId = $customer->getDefaultBilling();

            foreach($customer->getAddresses() as $address) {
                
                $temp = $address->toArray();

                if($temp['entity_id'] != $defaultBillingId) {
                    $temp['is_default'] = 0;
                }
                else {
                    $temp['is_default'] = 1;
                }

                $addresses[] = $temp;
                
            }
            
            return $addresses;
        }
    }

    public function getShippingMethod()
    {
        return $this->getShippingAddress()->getShippingRatesCollection();
    }

    public function getActiveShippingMethods()
    {
        $activeCarriers = $this->shipConfig->getActiveCarriers();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

		foreach($activeCarriers as $carrierCode => $carrierModel) {
		
			if( $carrierMethods = $carrierModel->getAllowedMethods() ) {

                $carrierTitle = $this->scopeConfig->getValue('carriers/'.$carrierCode.'/title');
                
                $code = $carrierCode . '_' . $carrierCode;

                $methods[$code] = $carrierTitle;
			}
			
        }
        
        return $methods;
    }

    public function getCheckoutConfig()
    {
        return $this->configProvider->getConfig();
    }

    public function getSerializedCheckoutConfig()
    {
        return $this->serializer->serialize($this->getCheckoutConfig());
    }

    public function getCheckoutSkinConfig()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        $configs = [
            'skin_button/radius',
            'skin_button/color',
            'skin_button/background',
            'skin_body/border',
            'skin_body/background',
            'skin_header/pagination_text',
            'skin_header/pagination_background',
            'skin_header/text',
            'skin_header/background'
        ];
        
        $skins = [];

        foreach($configs as $config) {
            $conf = str_replace('/', '_', $config); 
            $skins[$conf] = $this->scopeConfig->getValue(self::CHECKOUT_CONFIG. '/ulcheckout_skin/' . $config, $storeScope);
            // $skins[$conf] = self::CHECKOUT_CONFIG. '/ulcheckout_skin/)' . $config;
        }

        return $skins;
    }
    
    public function getPaymentMethod()
    {
        try {
            $methodCode = $this->checkoutSession->getQuote()->getPayment()->getMethod();
        }
        catch(\Exception $e) {
            $methodCode = '';
        }

        if($methodCode == '') {
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
                                            ->from('rra_tender_type', array('payment_method_code', )) 
                                            ->where('id=?', $tenderTypeId);
            $rowPayment			= $connection->fetchRow($sqlPayment);
            $methodCode 		= $rowPayment['payment_method_code'];
        }
        $payments = $this->paymentMethods;
        
        return $this->serializer->serialize($payments[$methodCode]);
    }
    public function serializedMethods()
    {
        $payments = $this->getPaymentComponent();
        foreach($payments as $payment):
            $paymentMethods[$payment['methodcode']] = $payment['tender_name'];
        endforeach;
        return $this->serializer->serialize($paymentMethods);
    }
    public function getPaymentComponent()
    {
        $paymentInfo	        = $this->getPaymentInfo();
        $payments               = $this->paymentMethods;
        $paymentMethods         = [];

        foreach($paymentInfo['tendertypes'] as $method):
            $methodCode = $method['methodcode'];
            $paymentMethods[] = ['methodcode' => $methodCode, 'tender_name' => $method['tendername'], 'component' => $payments[$methodCode]];
        endforeach;
        
        return $paymentMethods;
    }
    public function getTotals()
    {   
        $paymentInfo = $this->getPaymentInfo();
        $quote = $this->getQuote();
        $items = $quote->getAllVisibleItems();
        $tax   = $this->getTaxClass();

        $vatableSale    = 0;
        $vatExempt      = 0;
        $vatZeroRate    = 0;
        $vatAmount      = 0;
        $taxClass       = $tax['tax_class'];
        $convenienceFee = $quote->getConveniencFee();
        
        if($taxClass == 'VAT Inclusive') :

            foreach($items as $item) :
                $taxId      = $item->getTaxClassId();
                $taxName    = $this->getProductTaxClass($taxId);

                if($taxName == 'Taxable Goods'):
                    $price       = $item->getRowTotal();
                    $taxAmount   = $price * ($tax['tax_rate'] / 100);
                    $vatableSale += $price;
                    $vatAmount   += $taxAmount;
                else:
                    $vatExempt   += $item->getRowTotal();
                endif;

            endforeach;

        elseif($taxClass == 'VAT Exempt'):

            $vatExempt  = $quote->getSubtotal();

        elseif($taxClass == 'VAT Zero Rated'):

            $vatZeroRate = $quote->getSubtotal();

        endif;
		
		if($paymentInfo['available_amt'] < $quote->getGrandTotal()):
            $this->amountValid = false;
        else:
            $this->amountValid = true;
        endif;
		
		$total = $vatableSale + $vatExempt + $vatZeroRate + $vatAmount + $convenienceFee;

        $totals['tax_class']    = $taxClass;
        $totals['rate']         = $tax['tax_rate']; 
        $totals['order_amount'] = $quote->getSubtotal();
        $totals['vatable_sale'] = $vatableSale;    
        $totals['vat_exempt']   = $vatExempt;    
        $totals['vat_zeroRate'] = $vatZeroRate;    
        $totals['vat_amount']   = $vatAmount;    
        $totals['grand_total']  = $total;
        $totals['convenience']  = $convenienceFee;
        
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
                                            ->from('rra_emp_benefits', array('purchase_cap_id AS trans_type_id','available as available_amount','extension as extension_amount')) 
                                            ->where('entity_id=?', $customerId)
                                            ->where('is_active=?', 1); 

        $rowPurchase 		= $connection->fetchRow($sqlPurchaseCap);
        $availableAmt       = $rowPurchase['available_amount'];
		$availableAmt      += $rowPurchase['extension_amount'];
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
        $tenderType 		= $rowTenderType['tendertype_id'];

        $tenderTypeIds      = explode(',', $tenderType);
        $tenderTypes        = [];
        foreach($tenderTypeIds as $id) :
            $sqlPayment	    = $connection->select()
                                         ->from('rra_tender_type', array('payment_method_code', 'tender_name')) 
                                         ->where('id=?', $id);
            $rowPayment     = $connection->fetchRow($sqlPayment);
            
            $tenderTypes[]  = array('methodcode' => $rowPayment['payment_method_code'], 'tendername' => $rowPayment['tender_name']);
        endforeach;
    
        return ['tendertypes' => $tenderTypes, 'available_amt' => $availableAmt];
    }

    public function defaultShippingMethod()
    {
        $payment = $this->getPaymentInfo();
        
        if($payment['tendertypes'][0]['methodcode'] == 'cashondelivery') :
            return "minimumordervalue_minimumordervalue";
        else: 
            return "freeshipping_freeshipping";
        endif;
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
    
    public function validateAmount()
    {
        return $this->amountValid;
    }
	public function getContinueShoppingUrl()
    {
        $url = $this->getData('continue_shopping_url');
        if ($url === null) {
            $url = $this->checkoutSession->getContinueShoppingUrl(true);
            if (!$url) {
                $url = $this->_urlBuilder->getUrl();
            }
            $this->setData('continue_shopping_url', $url);
        }
        return $url;
    }
}