<?php
/**
 * DragonPay View XML.
 * @category  Unilab
 * @package   Unilab_DragonPay
 * @author    Kristian Claridad
 */
namespace Unilab\DragonPay\Controller\Payment;

use \Magento\Framework\Validator\Exception;

class Success extends \Unilab\DragonPay\Controller\Standard
{
    public function execute()
    {
        try {
            $session = $this->_getCheckout();
            
            // // Cleanup
            $session->unsQuoteId();

            // successAction
            $session = $this->_getCheckout();
            $session->setQuoteId($session->getDragonPayStandardQuoteId(true));
            $this->_getCheckout()->getQuote()->setIsActive(false)->save();     
            $this->_redirect('checkout/onepage/success', array('_secure'=>true));
            
        } catch (\Exception $e) {
            $this->logDebug(sprintf('successAction: %s', $e->getMessage()));
        }
    }
}
