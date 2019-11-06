<?php
/**
 * Healthcredits View XML.
 * @category  Unilab
 * @package   Healthcredits
 * @author    Kristian Claridad
 */
namespace Unilab\Healthcredits\Controller\Payment;

use \Magento\Framework\Validator\Exception;

class Response extends \Unilab\Healthcredits\Controller\Process
{
    public function execute()
    {
        // $process = $this->_objectManager->get('\Unilab\Healthcredits\Model\Api')->addData($_GET)->processResponse();
        try
        {   
            $process = $this->_objectManager->get('\Unilab\Healthcredits\Model\Api')->addData($_GET)->processResponse();
            return $this->getResponse()->setRedirect($this->base_url() . "healthcredits/payment/success");
        }
        catch(\Exception $e){
            $this->logDebug(sprintf('responseAction Failed: %s', $e->getMessage()));
            return $this->_redirect('healthcredits/payment/failed');
        }
    }
}
