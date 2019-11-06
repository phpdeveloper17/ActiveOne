<?php
/**
 * BpiSecurepay View XML.
 * @category  Unilab
 * @package   BpiSecurepay
 * @author    Kristian Claridad
 */
namespace Unilab\BpiSecurepay\Controller\Payment;

use \Magento\Framework\Validator\Exception;

class Failed extends \Unilab\BpiSecurepay\Controller\Payment
{
    public function execute()
    {
        try {
            $session = $this->_getCheckout();
            
            // // // Cleanup
            $session->unsQuoteId();

            // // failedAction
            $this->_getCheckout()->setQuoteId($this->_getCheckout()->getPaymentQuoteId(true));
            $this->_getCheckout()->getQuote()->setIsActive(false)->save();
            return $this->resultPageFactory->create();
            
        } catch (\Exception $e) {
            $this->logDebug(sprintf('failedAction: %s', $e->getMessage()));
        }
    }
}
