<?php
/**
 * BpiSecurepay View XML.
 * @category  Unilab
 * @package   BpiSecurepay
 * @author    Kristian Claridad
 */
namespace Unilab\BpiSecurepay\Controller\Payment;

use \Magento\Framework\Validator\Exception;

class Response extends \Unilab\BpiSecurepay\Controller\Payment
{
    public function execute()
    {
        try
        {   
            $process = $this->_objectManager->get('\Unilab\BpiSecurepay\Model\Api')->addData($_GET)->processResponse();
            return $this->getResponse()->setRedirect($this->base_url() . "bpisecurepay/payment/success");
        }
        catch(\Exception $e){
            $this->logDebug(sprintf('responseAction: %s', $e->getMessage()));
            return $this->_redirect('bpisecurepay/payment/failed');
        }
    }
}
