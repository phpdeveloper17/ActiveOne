<?php

namespace Unilab\Afptc\Model\Quote;

use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

class Freeshipping extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
   /**
    * @var \Magento\Framework\Pricing\PriceCurrencyInterface
    */
   protected $_priceCurrency;
   /**
    * Custom constructor.
    * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    */
   public function __construct(
       \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
   ){
       $this->_priceCurrency = $priceCurrency;
   }
   
   public function collect(
       \Magento\Quote\Model\Quote $quote,
       \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
       \Magento\Quote\Model\Quote\Address\Total $address
   )
   {
       parent::collect($quote, $shippingAssignment, $address);
            foreach ($this->_getAddressItems($address) as $item) {
                // if ($option = $item->getProduct()->getOptions('aw_afptc_rule')) {
                if ($option = $item->getProduct()->getCustomOption('aw_afptc_rule')) {
                    $item->setFreeShipping((bool) $this->_objectManager->create("Unilab\Afptc\Model\Rule")
                            ->load($option->getValue())->getFreeShipping()
                    );
                }
            }
       return $this;
   }
}
