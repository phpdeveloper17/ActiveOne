<?php
/**
 * Healthcredits View XML.
 * @category  Unilab
 * @package   Unilab_Healthcredits
 * @author    Kristian Claridad
 */
namespace Unilab\Healthcredits\Controller\Payment;

use \Magento\Framework\Validator\Exception;

class Failed extends \Unilab\Healthcredits\Controller\Process
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
