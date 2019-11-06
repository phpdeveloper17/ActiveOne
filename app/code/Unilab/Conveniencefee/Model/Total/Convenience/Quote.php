<?php
namespace Unilab\Conveniencefee\Model\Total\Convenience;

use Magento\Store\Model\ScopeInterface;

class Quote extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{

    protected $helperData;
	protected $_customerSession;
    protected $_paymentMethod;
    /**
     * Collect grand total address amount
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     */
    protected $quoteValidator = null;

    public function __construct(\Magento\Quote\Model\QuoteValidator $quoteValidator,
								\Magento\Customer\Model\Session $customerSession,
                                \Unilab\Conveniencefee\Helper\Data $helperData,
                                \Magento\Checkout\Model\Session $checkoutSession
                                )
    {
        $this->quoteValidator = $quoteValidator;
		$this->_customerSession = $customerSession;
        $this->helperData = $helperData;
        $this->_checkoutSession = $checkoutSession;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_paymentMethod = $this->_customerSession->getPaymentMethodTt();
        // echo $this->helperData->getConveniencefeeValue().'<br/>';
        // echo $this->gettaxRateFinal(1).'<br/>';
        // die;
    }

    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $address
    )
    {
        parent::collect($quote, $shippingAssignment, $address);
        $this->_setAmount(0);
        $this->_setBaseAmount(0);
       
        if (!count($shippingAssignment->getItems())) {
            return $this;
        }
        
        if($this->_paymentMethod == "dragonpay"){
            if($this->gettaxRateFinal($address->getCustomerId())>0){
            $ConFeeExclTax = $this->helperData->getConveniencefeeValue() / $this->gettaxRateFinal($this->_customerSession->getCustomerId());
           
            $ConFeeInclTax = $this->helperData->getConveniencefeeValue(); //15
            
            $address->setConveniencefee($ConFeeExclTax);
            $address->setBaseConveniencefee($ConFeeInclTax);
            
            $quote->setConveniencefee($ConFeeExclTax);
            $quote->setBaseConveniencefee($ConFeeInclTax);
                 
            $address->setGrandTotal($address->getGrandTotal() + $ConFeeExclTax);
            $address->setBaseGrandTotal($address->getBaseGrandTotal() + $ConFeeExclTax);

            // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/testconveniencefee.log');
            // $logger = new \Zend\Log\Logger();
            // $logger->addWriter($writer);
            // $logger->info('ConFeeExclTax='.$ConFeeExclTax.',ConFeeInclTax='.$ConFeeInclTax);
			}
        }else{
            
            $quote->setConvenienceFee(0);
            $quote->setBaseConvenienceFee(0);
            
        }
        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $address)
    {
        $con_feeExclTax = 0;
        $result = [];
        if($this->_paymentMethod == "dragonpay"){
            $con_feeExclTax = $address->getConveniencefee();
        }
        $result = [
            'code' => 'conveniencefee',
            'title' => $this->helperData->getConvenienceFeeLabel(),
            'value' => $con_feeExclTax
        ];
        return $result;
    }

    protected function gettaxRateFinal($customer_id){
        
        $Taxtype            = $this->_customerSession->getCustomer()->getTaxClassId();

        $tax                = $this->_objectManager->get('Magento\Tax\Model\Calculation');
        $Customerrates      = $tax->load($Taxtype, 'customer_tax_class_id');
        $taxRateID          = $Customerrates->getData();     
        // die;
        $connection         = $this->_getConnection();
       
        $SqlTaxRate         = $connection->select()->from('tax_calculation_rate', array('*')) 
                            ->where('tax_calculation_rate_id=?',@$taxRateID['tax_calculation_rate_id']);     
        $ResultTaxRate      = $connection->fetchRow($SqlTaxRate);
        
        // $ResultTaxRate['rate']      = 12.0000;
        $taxRateFinal       = 0;        
        
        if(!empty($ResultTaxRate['rate'])):
            $taxRateFinal       = ($ResultTaxRate['rate'] / 100) + 1;
        endif;

    
        return $taxRateFinal;
    }
    protected function _getConnection()
    {
        $this->_resource = $this->_objectManager->get("\Magento\Framework\App\ResourceConnection");
        $this->connection = $this->_resource->getConnection('core_write');
        return $this->connection;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     */
    protected function clearValues(\Magento\Quote\Model\Quote\Address\Total $total)
    {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);

    }
}
