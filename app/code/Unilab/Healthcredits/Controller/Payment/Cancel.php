<?php
/**
 * Healthcredits View XML.
 * @category  Unilab
 * @package   Unilab_Healthcredits
 * @author    Kristian Claridad
 */
namespace Unilab\Healthcredits\Controller\Payment;

use \Magento\Framework\Validator\Exception;

class Cancel extends \Unilab\Healthcredits\Controller\Process
{
    public function execute()
    {
        try {
            $session = $this->_getCheckout();
            $session->setQuoteId($this->_getCheckout()->getPaymentQuoteId(true));
            if ($session->getLastRealOrderId()) {
                $order = $this->_objectManager->get('Magento\Sales\Model\Order')->loadByIncrementId($session->getLastRealOrderId());
                if ($order->getId() && ($order->getState() == Order::STATE_PROCESSING || $order->getState() == Order::STATE_NEW || $order->getState() == Order::STATE_PENDING_PAYMENT) ) {
                    $order->cancel()->save();
                }
            }
            $this->_redirect('checkout/cart');

        } catch (\Exception $e) {
            $this->logWarning(sprintf('failedAction: %s', $e->getMessage()));
        }
    }
}
