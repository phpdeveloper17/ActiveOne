<?php
/**
 * DragonPay View XML.
 * @category  Unilab
 * @package   Unilab_DragonPay
 * @author    Kristian Claridad
 * RRA/Admincontroller/ApiController.php
 */
namespace Unilab\DragonPay\Controller\Payment;

use \Magento\Framework\Validator\Exception;

class Response extends \Unilab\DragonPay\Controller\Standard
{
    protected $jsonHelper;
    public function execute()
    {
        $orderStatus = null;
        $response = array();
        $process = null;
        if(isset($_POST['status'])){
            $orderStatus = strtolower($_POST['status']);
        }
        $this->logError(sprintf('OrderStatus: %s', $orderStatus));
        try {
            
            if($orderStatus === 's'){
                $process = $this->_objectManager->create('\Unilab\DragonPay\Model\Handler')->addData($_POST)->processResponse();
                // $sapresponse = $this->_objectManager->get('aonewebservice/order_sendtosap')->send($_POST['txnid'], "N");
                $response['success']    = true; 
                $response['error']  = 'Successfully received!';
                $_POST['order_status'] = 'Success Order [POST]';
                $_POST['fullUrlResponse'] = $this->getfullUrl();
                file_put_contents('./debug-dragon-payment.txt', print_r($_POST,1).PHP_EOL,FILE_APPEND);
                // Mage::log($sapresponse, null, 'send_To_SAP.log');
            }
            
            if(isset($_GET['status'])){
                $orderStatus = strtolower($_GET['status']); 
            }
            
            if($orderStatus === 's'){
                $process = $this->_objectManager->create('\Unilab\DragonPay\Model\Handler')->addData($_GET)->processResponse();
                // $sapresponse = $this->_objectManager->get('aonewebservice/order_sendtosap')->send($_GET['txnid'], "N");
                $response['success']    = true; 
                $response['error']  = 'Successfully received!'; 
                $_GET['order_status'] = 'Success Order [GET]';
                $_GET['fullUrlResponse'] = $this->getfullUrl();
                file_put_contents('./debug-dragon-payment.txt', print_r($_GET,1).PHP_EOL,FILE_APPEND);
                // Mage::log($sapresponse, null, 'send_To_SAP.log');
            }
            $this->jsonHelper = $this->_objectManager->create('\Magento\Framework\Json\Helper\Data');
            $this->getResponse()->setBody($this->jsonHelper->jsonEncode($response));                     
            
            if($process == false && $_GET['status'] == 'P'){
                $process = $this->_objectManager->create('\Unilab\DragonPay\Model\Handler')->addData($_GET)->processResponse();
                $_GET['order_status'] = 'Pending Order';
                $_GET['fullUrlResponse'] = $this->getfullUrl();
                file_put_contents('./debug-dragon-payment.txt', print_r($_GET,1).PHP_EOL,FILE_APPEND);
                $this->_redirect('dragonpay/payment/review', array("refno"=>$_GET['refno']));  
            }
            elseif($process == false && $_GET['status'] != 'S'){
                $_GET['order_status'] = 'Failed Order';
                file_put_contents('./debug-dragon-payment.txt', print_r($_GET,1).PHP_EOL,FILE_APPEND);
                $this->_redirect('dragonpay/payment/failed');  
            }
            elseif($_GET['status'] == 'S'){
                $this->_redirect('dragonpay/payment/success', array("refno"=>$_GET['refno']));     
            }
        } catch (\Exception $e) {
            $this->_objectManager->create('\Magento\Framework\Message\ManagerInterface')->addError('DragonPay Error: '.$e->getMessage());
            $this->logWarning(sprintf('responseAction: %s', $e->getMessage()));
            return $this->_redirect('dragonpay/payment/failed');
        }
    }
}
