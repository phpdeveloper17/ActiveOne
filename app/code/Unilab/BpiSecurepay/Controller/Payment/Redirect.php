<?php
/**
 * BpiSecurepay View XML.
 * @category  Unilab
 * @package   BpiSecurepay
 * @author    Kristian Claridad
 */
namespace Unilab\BpiSecurepay\Controller\Payment;

use \Magento\Framework\Validator\Exception;

class Redirect extends \Unilab\BpiSecurepay\Controller\Payment
{
    public function execute()
    {
        try {
            $this->getcustomOrder();
            $session = $this->_getCheckout();
           
            $session->unsQuoteId();

            // redirect
            $this->redirectAction();
            
        } catch (\Exception $e) {
            $this->logDebug(sprintf('redirectAction: %s', $e->getMessage()));
        }
    }
}
