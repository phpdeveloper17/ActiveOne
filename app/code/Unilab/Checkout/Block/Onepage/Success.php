<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Checkout\Block\Onepage;

use Magento\Customer\Model\Context;
use Magento\Sales\Model\Order;

/**
 * One page checkout success page
 *
 * @api
 */
class Success extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_checkoutSession = $checkoutSession;
        $this->_orderConfig = $orderConfig;
        $this->_isScopePrivate = true;
        $this->httpContext = $httpContext;
        $this->currency = $currency;
        $this->product = $product;
        $this->customerSession = $customerSession;
        $this->groupRepository = $groupRepository;
        $this->resource = $resource;
    }

    /**
     * Render additional order information lines and return result html
     *
     * @return string
     */
    public function getAdditionalInfoHtml()
    {
        return '';//$this->_layout->renderElement('order.success.additional.info');
    }

    /**
     * Initialize data and prepare it for output
     *
     * @return string
     */
    protected function _beforeToHtml()
    {
        $this->prepareBlockData();
        return parent::_beforeToHtml();
    }

    /**
     * Prepares block data
     *
     * @return void
     */
    protected function prepareBlockData()
    {
        $order = $this->_checkoutSession->getLastRealOrder();

        $this->addData(
            [
                'is_order_visible' => $this->isVisible($order),
                'view_order_url' => $this->getUrl(
                    'sales/order/view/',
                    ['order_id' => $order->getEntityId()]
                ),
                'print_url' => $this->getUrl(
                    'sales/order/print',
                    ['order_id' => $order->getEntityId()]
                ),
                'can_print_order' => $this->isVisible($order),
                'can_view_order'  => $this->canViewOrder($order),
                'order_id'  => $order->getIncrementId(),
                'order_items' => $order->getAllItems()
            ]
        );
    }

    public function getTotals()
    {   
        $order = $this->_checkoutSession->getLastRealOrder();
        $items = $order->getAllVisibleItems();
        $tax   = $this->getTaxClass();

        $vatableSale    = 0;
        $vatExempt      = 0;
        $vatZeroRate    = 0;
        $vatAmount      = 0;
        $taxClass       = $tax['tax_class'];
        $convenienceFee = $order->getConveniencFee();
        
        if($taxClass == 'VAT Inclusive') :

            foreach($items as $item) :
                $taxId      = $this->product->create()->load($item->getProductId())->getTaxClassId();
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

            $vatExempt  = $order->getSubtotal();

        elseif($taxClass == 'VAT Zero Rated'):

            $vatZeroRate = $order->getSubtotal();

        endif;
        

        $totals['tax_class']    = $taxClass;
        $totals['rate']         = $tax['tax_rate']; 
        $totals['order_amount'] = $order->getSubtotal();
        $totals['vatable_sale'] = $vatableSale;    
        $totals['vat_exempt']   = $vatExempt;    
        $totals['vat_zeroRate'] = $vatZeroRate;    
        $totals['vat_amount']   = $vatAmount;    
        $totals['grand_total']  = $order->getGrandTotal();
        $totals['convenience']  = $convenienceFee;
        $totals['shipping']     = $order->getData();
        
        return $totals;
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

    private function getProductTaxClass($id) 
    {
        $connection = $this->getConnection();

        $sqlTax     = $connection->select()->from('tax_class', array('class_name')) 
                                ->where('class_id=?', $id);
        $taxClass   = $connection->fetchRow($sqlTax);

        return $taxClass['class_name'];
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

    private function getConnection()
	{
		return $this->resource->getConnection();
	}
    /**
     * Is order visible
     *
     * @param Order $order
     * @return bool
     */
    protected function isVisible(Order $order)
    {
        return !in_array(
            $order->getStatus(),
            $this->_orderConfig->getInvisibleOnFrontStatuses()
        );
    }

    /**
     * Can view order
     *
     * @param Order $order
     * @return bool
     */
    protected function canViewOrder(Order $order)
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH)
            && $this->isVisible($order);
    }

    /**
     * @return string
     * @since 100.2.0
     */
    public function getContinueUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    public function formatPrice($price)
    {
        return $this->currency->format($price, array('symbol' => '₱'), false, false);
    }
}
