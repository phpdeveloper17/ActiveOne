<?php
namespace Unilab\Conveniencefee\Model\Total\Vatablesale;

class Quote extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
	
    protected $_code = 'vatablesale';
 
    protected $helperData;
	protected $_customerSession;

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
                                \Unilab\Conveniencefee\Helper\Data $helperData)
    {
        $this->quoteValidator = $quoteValidator;
		$this->_customerSession = $customerSession;
        $this->helperData = $helperData;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $address
    )
    {
        parent::collect($quote, $shippingAssignment, $address);
        if (!count($shippingAssignment->getItems())) {
            return $this;
        }
    }
 
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $address)
    {
		$con_feeExclTax = 0;	
		if($quote->getPayment()->getMethod() == "dragonpay"){	
			$con_feeExclTax = $address->getConvenienceFee();	
		}

		$subtotalExcltax = $address->getSubtotal() + $con_feeExclTax;
			
        $result = [];
            $result = [
                'code' => $this->_code,
                'title' => __('VATable Sales'),
                'value' => $subtotalExcltax
            ];

        return $result;
		
    }
	
}
