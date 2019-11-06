<?php
/*
Store->Sales Order Print In Customer
*/
/**
 * Tax totals modification block. Can be used just as subblock of \Magento\Sales\Block\Order\Totals
 */

namespace Unilab\Conveniencefee\Block\Sales\Totals;

class Conveniencefee extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magecomp\Extrafee\Helper\Data
     */
    protected $_dataHelper;
   

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_currency;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Unilab\Conveniencefee\Helper\Data $dataHelper,
        \Magento\Directory\Model\Currency $currency,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_dataHelper = $dataHelper;
        $this->_currency = $currency;
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->_currency->getCurrencySymbol();
    }

    /**
     *
     *
     * @return $this
     */
    public function initTotals()
    {
        $this->getParentBlock();
        $this->getOrder();
        $this->getSource();
        $parent = $this->getParentBlock();
        $this->_order = $parent->getOrder();
        $this->_source = $parent->getSource();
        $this->_totals = array();
        $parent->removeTotal('base_grandtotal');
        
        $this->_totals['subtotal'] = new \Magento\Framework\DataObject(
            [
                'code'      => 'subtotal',
                'value'     => $this->_source->getSubtotal(),
                'base_value'=> $this->_source->getBaseSubtotal(),
                'label'     => __('Order Amount')
            ]
        );
        
        $amount = $this->_order->getConveniencefee();

        if ($amount && $this->_order->getPayment()->getMethod() == "dragonpay" ) {
            $this->_totals['conveniencefee'] = new \Magento\Framework\DataObject(
                [
                    'code'      => 'conveniencefee',
                    'value'     => $amount,
                    'base_value'=> $amount,
                    'label'     => $this->_dataHelper->getConvenienceFeeLabel()
                ]
            );
        }
        
        /**
         * Add shipping
         */
         
        if (!$this->_source->getIsVirtual() && ((float) $this->_source->getShippingAmount() || $this->_source->getShippingDescription()))
        {
            $this->_totals['shipping'] = new \Magento\Framework\DataObject(
                [
                    'code'      => 'shipping',
                    'value'     => $this->_source->getShippingAmount(),
                    'base_value'=> $this->_source->getBaseShippingAmount(),
                    'label'     => 'Shipping & Handling'
                ]
            );
        }
        $this->_totals['vatablesales'] = new \Magento\Framework\DataObject(
            [
                'code'      => 'vatablesales',
                'value'     => $this->_source->getSubtotal() + $amount,
                'base_value'=> $this->_source->getBaseSubtotal() + $amount,
                'label'     => 'VATable Sales'
            ]
        );
        /**
         * Add discount
         */
        if (((float)$this->_source->getDiscountAmount()) != 0) {
            if ($this->_source->getDiscountDescription()) {
                $discountLabel = __('Discount (%s)', $this->_source->getDiscountDescription());
            } else {
                $discountLabel = __('Discount');
            }
            $this->_totals['discount'] = new \Magento\Framework\DataObject(
                [
                    'code'      => 'discount',
                    'value'     => $this->_source->getDiscountAmount(),
                    'base_value'=> $this->_source->getBaseDiscountAmount(),
                    'label'     => $discountLabel
                ]
            );
        }

        $this->_totals['grand_total'] = new \Magento\Framework\DataObject(
            [
                'code'      => 'grand_total',
                'strong'    => true,
                'value'     => $this->_source->getGrandTotal(),
                'base_value'=> $this->_source->getBaseGrandTotal(),
                'label'     => 'Grand Total',
                'area'      => 'footer'
            ]
        );
        foreach ($this->_totals as $key => $value) {
            $parent->addTotal($this->_totals[$key], $key);
        }
        
        return $this;
    }
}
