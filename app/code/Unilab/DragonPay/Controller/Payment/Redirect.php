<?php
/**
 * DragonPay View XML.
 * @category  Unilab
 * @package   DragonPay
 * @author    Kristian Claridad
 */
namespace Unilab\DragonPay\Controller\Payment;

use \Magento\Framework\Validator\Exception;

class Redirect extends \Unilab\DragonPay\Controller\Standard
{
    public function execute()
    {
        try {
			
            $session = $this->_getCheckout();
            
            // // Cleanup
            $session->unsQuoteId();

            // redirect
            $orderId = $session->getLastRealOrderId();
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($orderId);
            if (is_null($order) || is_null($order->getId())) {
                $session->unsLastRealOrderId();
                $this->logError(sprintf('redirect Action Failed: %s', 'Empty Order'));
                return $this->_404();
            }else{
                $this->logError(sprintf('redirect Action Success: %s',''));
				$session
                ->setLastOrderId($order->getId())
                ->setLastRealOrderId($order->getIncrementId())
                ->setLastOrderStatus($order->getStatus());
                return $this->redirectAction();
				$session->unsLastRealOrderId();
            }
            
        } catch (\Exception $e) {
            $this->logWarning(sprintf('redirect Action Failed: %s', $e->getMessage()));
        }
    }
}
