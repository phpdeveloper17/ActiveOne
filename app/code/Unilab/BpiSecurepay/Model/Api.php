<?php
/**
 * BpiSecurepay View XML.
 * @category  Unilab
 * @package   BpiSecurepay
 * @author    Kristian Claridad
 */
namespace Unilab\BpiSecurepay\Model;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment\Transaction;
use Unilab\BpiSecurepay\Model\Payment\AbstractPayment;
use Unilab\BpiSecurepay\Api\Data\BpiSecurepayInterface;
use \Magento\Framework\Validator\Exception;

class Api extends \Magento\Payment\Model\Method\AbstractMethod
{
  
	private $_order;
	public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\UrlInterface $urlInterface,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Unilab\BpiSecurepay\Helper\Data $coreData,
        \Magento\Framework\DB\Transaction $transactionFactory //resource_transaction 
    ) {
        $this->_objectManager = $objectManager;
        $this->_urlInterface = $urlInterface;
        $this->_logger = $logger;
        $this->_scopeConfig = $scopeConfig;
        $this->_coreHelper = $coreData;
        $this->_transactionFactory = $transactionFactory;

        $this->_storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $this->response = $this->_objectManager->get('Magento\Framework\App\ResponseInterface');
        $this->_invoiceService = $this->_objectManager->get('\Magento\Sales\Model\Service\InvoiceService');
    }

    
    public function getCoreSession(){
        return $this->_objectManager->get('\Magento\Framework\Session\SessionManagerInterface');
    }
    
    public function getCheckoutSession(){
        return $this->_objectManager->get('\Magento\Checkout\Model\Session');
    }

    protected function _getOrder()
    {
        if (empty($this->_order)) {
            // get proper order
            $id = $this->getData('vpc_MerchTxnRef');
            
            $this->_order = $this->_objectManager->get('Magento\Sales\Model\Order')->loadByIncrementId($id);
           
            if (!$this->_order->getId()) {
              $this->response->setHeader('HTTP/1.1 503 Service Unavailable', 'binary', true);
                exit;
            }

            // re-initialize config with the method code and store id
            $methodCode = $this->_order->getPayment()->getMethod();
            
            if(!$this->_scopeConfig->getValue("payment/{$methodCode}/active",\Magento\Store\Model\ScopeInterface::SCOPE_STORE)){
                throw new \LogicException(sprintf('Method "%s" is not available.', $methodCode));
            }
        }
        return $this->_order; 
    }
    public function _getPaymentConfig(){
        return $this->_objectManager->get('\Unilab\BpiSecurepay\Model\Config')->getPaymentConfig();
    }
    public function processResponse(){

        $vpc_Txn_Secure_Hash = $this->getData("vpc_SecureHash");
        
        $errorExists = false;
        
        $SECURE_SECRET = $this->_getPaymentConfig()->getMerchantSecureHashSecret();
        
        $data = $this->getData();
        
        
        file_put_contents('./debug-bpi-payment.txt', print_r($data,1).PHP_EOL,FILE_APPEND);
        
        
        if (strlen($SECURE_SECRET) > 0 && $this->getData("vpc_TxnResponseCode") != "7" && $this->getData("vpc_TxnResponseCode") != "No Value Returned") 
        {

            $md5HashData = $this->_getPaymentConfig()->getMerchantSecureHashSecret();
           
            unset($data['vpc_SecureHash']);
            unset($data['vpc_SecureHashType']);
            $HashData = '';
            // sort all the incoming vpc response fields and leave out any with no value
            foreach($data as $key => $value) {
                if ($key != "vpc_SecureHash" and strlen($value) > 0) {
                    $HashData .= $key . "=" . $value . "&";
                }
            }
            
            $concathashdata = rtrim($HashData,"&");
            $securhash = strtoupper(hash_hmac('SHA256',$concathashdata, pack("H*",$md5HashData)));
            
            // Validate the Secure Hash (remember MD5 hashes are not case sensitive)
            // This is just one way of displaying the result of checking the hash.
            // In production, you would work out your own way of presenting the result.
            // The hash check is all about detecting if the data has changed in transit.
            if (strtoupper($vpc_Txn_Secure_Hash) == $securhash) {
                // Secure Hash validation succeeded, add a data field to be displayed
                // later.
                // $this->_getOrder()->setState(Order::STATE_PROCESSING); #set state = processing
                $hashValidated = __('Success');
            } else {
                // Secure Hash validation failed, add a data field to be displayed
                // later.
                $hashValidated = __('Invalid Hash');
                $errorExists = true;
            }
           
        } 
                
                
        $txnResponseCode = $this->null2unknown($data["vpc_TxnResponseCode"]);

        if(!$errorExists && $this->getData("vpc_TxnResponseCode") == "0" && $this->_getOrder()->getState() == Order::STATE_PROCESSING)
        {
            $payment = $this->_getOrder()->getPayment();
            $orderStatus=Order::STATE_PROCESSING;
            $this->getCoreSession()->setTransactionNo($this->getData('vpc_ReceiptNo'));

            $payment->setTransactionId($this->getData('vpc_ReceiptNo'))
            ->setParentTransactionId($this->getData('vpc_MerchTxnRef'))
            ->setShouldCloseParentTransaction(false)
            ->setIsTransactionClosed(false)
            ->setAdditionalInformation('TransactionNo:'.$this->getData('vpc_TransactionNo').PHP_EOL.'ReceiptNo:'.$this->getData('vpc_ReceiptNo').PHP_EOL.'Message:'.$this->getData('vpc_Message'))
            ->setStatus($orderStatus);
            $payment->setPreparedMessage('');

            $this->_getOrder()->save();

            if(!$this->_getOrder()->canInvoice())
            {
                throw new \LogicException('Cannot create an invoice.');
            }

            $invoice = $this->_invoiceService->prepareInvoice($this->_getOrder());
            
            if (!$invoice->getTotalQty()) {
                throw new \LogicException('Cannot create an invoice without products.');
            }
            // capture request order
            $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
            $invoice->register();

            $this->_transactionFactory->addObject($invoice);
            $this->_transactionFactory->addObject($invoice->getOrder());
            $this->_transactionFactory->save();
            
            
            $this->sendemailto();
            $this->_eventManager = $this->_objectManager->get('Magento\Framework\Event\ManagerInterface');
            $this->_eventManager->dispatch('unilab_payment_captured_after', ['order' => $this->_getOrder()]);
            
            $this->_objectManager->get('\Magento\Framework\Message\ManagerInterface')->addSuccess(__('Your transaction ID is '.$this->getData('vpc_ReceiptNo')));
            $this->setTxnId($this->getData('vpc_ReceiptNo'));
            }
        else
        {

            $errMsg = $this->getResponseDescription($txnResponseCode);
            
            $this->updatePayment(true,Order::STATE_NEW);
            $this->_getOrder()->addStatusHistoryComment($errMsg);
            $this->_getOrder()->save();
            
            $orderViewURL = $this->_urlInterface->getUrl('sales/order/view',['order_id' => $this->_getOrder()->getId()]);

            // $this->getCheckoutSession()->addError($errMsg.'. '.sprintf('Order has been cancelled (Reference: <a href="%s" alt="">%s</a>).', $orderViewURL, $this->getData('vpc_MerchTxnRef')));
            
            throw new \Exception($hashValidated);

        }
    }
    protected function sendemailto()
    {
        $invoice = $this->_invoiceService->prepareInvoice($this->_getOrder());
        $invoice->getOrder()->setIsCustomerNotified(true);
        $invoice->getOrder()->setIsInProcess(true);
        $invoice->getOrder()->addStatusHistoryComment('Notified customer about invoice.')->setIsCustomerNotified(true);
        
        $this->InvoiceSender($invoice);
        if($this->_getOrder()->canInvoice())
        {
            // Mage::getModel('aonewebservice/order_sendtosap')->send($this->getData('vpc_MerchTxnRef'), "N");      
        }

    }
    function InvoiceSender($invoice){
        $invoiceSender = $this->_objectManager->get('Magento\Sales\Model\Order\Email\Sender\InvoiceSender');
        $invoiceSender->send($invoice);
        return true;
    }
    /**
     * Process Update payment either full/partial
     */
    public function updatePayment($isPending=false,$orderStatus=Order::STATE_PROCESSING){
        $payment = $this->_getOrder()->getPayment();
        $payment->setTransactionId($this->getData('vpc_ReceiptNo'))
                ->setParentTransactionId($this->getData('vpc_MerchTxnRef'))
                ->setShouldCloseParentTransaction(false)
                ->setIsTransactionClosed(false)
                ->setAdditionalInformation('TransactionNo:'.$this->getData('vpc_TransactionNo').PHP_EOL.'ReceiptNo:'.$this->getData('vpc_ReceiptNo').PHP_EOL.'Message:'.$this->getData('vpc_Message'))
                ->setStatus($orderStatus)
                ->setIsTransactionPending($isPending);
		$payment->save();
		
		return $payment;
    }

    /**
     * Capture payment transaction
     */
    protected function _registerPaymentCapture()
    {   
        $this->updatePayment(false,Order::STATE_PROCESSING);
        
        $payment = $this->_getOrder()->getPayment();
        
        $payment->setPreparedMessage('');
        
        $this->_getOrder()->save();
        
        try {
            if(!$this->_getOrder()->canInvoice())
            {
                // throw new \LogicException('Cannot create an invoice');
            }
            $invoice = $this->_invoiceService->prepareInvoice($this->_getOrder());
            if (!$invoice->getTotalQty()) {
                // throw new \LogicException('Cannot create an invoice without products.');
            }
            $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
            $invoice->register();
            
            $invoice->getOrder()->setIsCustomerNotified(true);
            $invoice->getOrder()->setIsInProcess(true);
            $invoice->getOrder()->addStatusHistoryComment('Notified customer about invoice.')
                        ->setIsCustomerNotified(true);
            
            $this->InvoiceSender($invoice);
            
            $transactionSave = $this->_transactionFactory->addObject($invoice)
                ->addObject($invoice->getOrder());
            
            $transactionSave->save();
            $this->_eventManager = $this->_objectManager->get('Magento\Framework\Event\ManagerInterface');
            $this->_eventManager->dispatch('unilab_payment_captured_after', ['order' => $this->_getOrder()]);
        
        }
        catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_getOrder()->addStatusHistoryComment('BPI Payment Secure: Exception occurred during _registerPaymentCapture() action. Exception message: '.$e->getMessage(), false);
            throw $e;
        }
        
    }
    protected function _buildUrl($url)
    {
        $url = $this->_urlInterface->getUrl($url, ['_secure' => true]);
        $url = $this->_urlInterface->sessionUrlVar($url);
        return $url;
    }
    
    public function getResponseDescription($responseCode) {
        switch ($responseCode) {
            case "0" : $result = "Transaction Successful"; break;
            case "?" : $result = "Transaction status is unknown"; break;
            case "1" : $result = "Unknown Error"; break;
            case "2" : $result = "Bank Declined Transaction"; break;
            case "3" : $result = "No Reply from Bank"; break;
            case "4" : $result = "Expired Card"; break;
            case "5" : $result = "Insufficient funds"; break;
            case "6" : $result = "Error Communicating with Bank"; break;
            case "7" : $result = "Payment Server System Error"; break;
            case "8" : $result = "Transaction Type Not Supported"; break;
            case "9" : $result = "Bank declined transaction (Do not contact Bank)"; break;
            case "A" : $result = "Transaction Aborted"; break;
            case "C" : $result = "Transaction Cancelled"; break;
            case "D" : $result = "Deferred transaction has been received and is awaiting processing"; break;
            case "F" : $result = "3D Secure Authentication failed"; break;
            case "I" : $result = "Card Security Code verification failed"; break;
            case "L" : $result = "Shopping Transaction Locked (Please try the transaction again later)"; break;
            case "N" : $result = "Cardholder is not enrolled in Authentication scheme"; break;
            case "P" : $result = "Transaction has been received by the Payment Adaptor and is being processed"; break;
            case "R" : $result = "Transaction was not processed - Reached limit of retry attempts allowed"; break;
            case "S" : $result = "Duplicate SessionID (OrderInfo)"; break;
            case "T" : $result = "Address Verification Failed"; break;
            case "U" : $result = "Card Security Code Failed"; break;
            case "V" : $result = "Address Verification and Card Security Code Failed"; break;
            default  : $result = "Unable to be determined"; 
        }
        return $result;
    }

    /*
    * This method uses the verRes status code retrieved from the Digital
    * Receipt and returns an appropriate description for the QSI Response Code
    *
    * @param statusResponse String containing the 3DS Authentication Status Code
    * @return String containing the appropriate description
    */  
    public function getStatusDescription($statusResponse) {
        if ($statusResponse == "" || $statusResponse == "No Value Returned") {
            $result = "3DS not supported or there was no 3DS data provided";
        } else {
            switch ($statusResponse) {
                Case "Y"  : $result = "The cardholder was successfully authenticated."; break;
                Case "E"  : $result = "The cardholder is not enrolled."; break;
                Case "N"  : $result = "The cardholder was not verified."; break;
                Case "U"  : $result = "The cardholder's Issuer was unable to authenticate due to some system error at the Issuer."; break;
                Case "F"  : $result = "There was an error in the format of the request from the merchant."; break;
                Case "A"  : $result = "Authentication of your Merchant ID and Password to the ACS Directory Failed."; break;
                Case "D"  : $result = "Error communicating with the Directory Server."; break;
                Case "C"  : $result = "The card type is not supported for authentication."; break;
                Case "S"  : $result = "The signature on the response received from the Issuer could not be validated."; break;
                Case "P"  : $result = "Error parsing input from Issuer."; break;
                Case "I"  : $result = "Internal Payment Server system error."; break;
                default   : $result = "Unable to be determined"; break;
            }
        }
        return $result;
    }
    
    /*
     * If input is null, returns string "No Value Returned", else returns input
     */
    public function null2unknown($data) {
        if ($data == "") {
            return "No Value Returned";
        } else {
            return $data;
        }
    }
}