<?php
/**
 * DragonPay View XML.
 * @category  Unilab
 * @package   Unilab_DragonPay
 * @author    Kristian Claridad
 */
namespace Unilab\DragonPay\Controller\Payment;

use \Magento\Framework\Validator\Exception;

class Failed extends \Unilab\DragonPay\Controller\Standard
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
            $this->logWarning(sprintf('ActionFailed: %s', $e->getMessage()));
        }
    }
}
