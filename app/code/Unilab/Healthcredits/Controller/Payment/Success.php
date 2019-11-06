<?php
/**
 * Healthcredits View XML.
 * @category  Unilab
 * @package   Unilab_Healthcredits
 * @author    Kristian Claridad
 */
namespace Unilab\Healthcredits\Controller\Payment;

use \Magento\Framework\Validator\Exception;

class Success extends \Unilab\Healthcredits\Controller\Process
{
    public function execute()
    {
        try {
            $session = $this->_getCheckout();
            
            // // Cleanup
            $session->unsQuoteId();

            // successAction
            $session->setQuoteId($this->_getCheckout()->getPaymentQuoteId(true));
            $this->_getCheckout()->getQuote()->setIsActive(false)->save();     
            $this->_redirect('checkout/onepage/success', array('_secure'=>true));
            
        } catch (\Exception $e) {
            $this->logDebug(sprintf('successAction: %s', $e->getMessage()));
        }
    }
}
