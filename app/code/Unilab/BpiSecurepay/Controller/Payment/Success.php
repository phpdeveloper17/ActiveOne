<?php
/**
 * BpiSecurepay View XML.
 * @category  Unilab
 * @package   BpiSecurepay
 * @author    Kristian Claridad
 */
namespace Unilab\BpiSecurepay\Controller\Payment;

use \Magento\Framework\Validator\Exception;

class Success extends \Unilab\BpiSecurepay\Controller\Payment
{
    public function execute()
    {
        try {
            $this->getcustomOrder();
            $session = $this->_getCheckout();
            
            // // Cleanup
            $session->unsQuoteId();

            // successAction
            $this->successAction();
            
        } catch (\Exception $e) {
            $this->logDebug(sprintf('successAction: %s', $e->getMessage()));
        }
    }
}
