<?php
/**
 * Healthcredits View XML.
 * @category  Unilab
 * @package   Healthcredits
 * @author    Kristian Claridad
 */
namespace Unilab\Healthcredits\Controller\Payment;

use \Magento\Framework\Validator\Exception;

class Redirect extends \Unilab\Healthcredits\Controller\Process
{
    public function execute()
    {
        try {
            $session = $this->_getCheckout();
            
            // // Cleanup
            $session->unsQuoteId();

            // redirect
            $orderId = $session->getLastRealOrderId();
            $order = $this->_objectManager->get('Magento\Sales\Model\Order')->loadByIncrementId($orderId);
            if (is_null($order) || is_null($order->getId())) {
                $session->unsLastRealOrderId();
                $this->logError(sprintf('redirect Action Failed: %s', 'Empty Order'));
                return $this->_404();
            }else{
                $this->logError(sprintf('redirect Action Success: %s',''));
                $this->redirectAction();
            }
            
        } catch (\Exception $e) {
            $this->logWarning(sprintf('redirect Action Failed: %s', $e->getMessage()));
        }
        
    }
}
