<?php
/**
 * DragonPay View XML.
 * @category  Unilab
 * @package   Unilab_DragonPay
 * @author    Kristian Claridad
 */
namespace Unilab\DragonPay\Controller\Payment;

use \Magento\Framework\Validator\Exception;

class Response extends \Unilab\DragonPay\Controller\Standard
{
    public function execute()
    {
        $expectedKeys = array('txnid','refno','message','digest','status');
        $data = $this->getRequest()->getParams();

        if (count(array_keys($data)) != count($expectedKeys)){
            $this->_redirect('dragonpay/payment/cancel'); // Cancel the order
            $this->logError(sprintf('responseAction: %s', 'Invalid count Request'));
        }

        try {
            $process = $this->_objectManager->get('\Unilab\DragonPay\Model\Handler')->processRequest($data);
        } catch (\Exception $e) {
            $this->_objectManager->get('\Magento\Framework\Message\ManagerInterface')->addError('DragonPay Error: '.$e->getMessage());
            $this->logWarning(sprintf('responseAction: %s', $e->getMessage()));
            return $this->_redirect('dragonpay/payment/failed');
        }
    }

}
