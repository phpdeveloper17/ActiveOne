<?php
/**
 * Healthcredits View XML.
 * @category  Unilab
 * @package   Unilab_Healthcredits
 * @author    Kristian Claridad
 */
namespace Unilab\Healthcredits\Model;

use \Magento\Sales\Model\Order;
use \Magento\Framework\Validator\Exception;

class Api extends \Magento\Payment\Model\Method\AbstractMethod {
    
    protected $_order = null;
    protected $responseCode = '';

    protected $_storeManager;
    protected $_redirect;
    protected $response;
    protected $_invoiceService;
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\UrlInterface $urlInterface,
        \Unilab\Healthcredits\Logger\Logger $loggerInteface,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Unilab\Healthcredits\Helper\Data $coreData,
        \Magento\Framework\DB\Transaction $transactionFactory //resource_transaction 
    ) {
        $this->_objectManager = $objectManager;
        $this->_urlInterface = $urlInterface;
        $this->_logger = $loggerInteface;
        $this->_scopeConfig = $scopeConfig;
        $this->_coreHelper = $coreData;
        $this->_transactionFactory = $transactionFactory;

        $this->_storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $this->_redirect = $this->_objectManager->get('\Magento\Framework\App\Response\Http');
        $this->response = $this->_objectManager->get('Magento\Framework\App\ResponseInterface');
        $this->_invoiceService = $this->_objectManager->get('\Magento\Sales\Model\Service\InvoiceService');

    }

    public function getCoreSession(){
        return $this->_objectManager->get('\Magento\Framework\Session\SessionManagerInterface');
    }   
    
    public function getCheckoutSession(){
        return $this->_objectManager->get('\Magento\Checkout\Model\Session');
    }
    public function getCustomerSession(){
        return $this->_objectManager->get('Magento\Customer\Model\Session');
    }
    protected function _getOrder()
    {
        if (empty($this->_order)) {
            $id = $this->getData('rra_ordernumber');
            
            $this->_order = $this->_objectManager->get('Magento\Sales\Model\Order')->loadByIncrementId($id);
            if (!$this->_order->getId()) {
               $this->response->setHeader('HTTP/1.1 503 Service Unavailable', 'binary', true);
               exit;
            }
            // re-initialize config with the method code and store id
            $methodCode = $this->_order->getPayment()->getMethod();

            if(!$this->_scopeConfig->getValue("payment/{$methodCode}/active")){
                throw new \LogicException(sprintf('Method "%s" is not available.', $methodCode));
            }
        }
        return $this->_order;
    }
    public function processResponse() {
        $this->_getOrder()->setState(Order::STATE_PROCESSING); #set state = processing
		$errMsg ='';
		$errorExists = false;		
		$data = $this->getData();
		$data['fullUrl'] = $this->getfullUrl();
		file_put_contents('./debug-healthcredits-payment.txt', print_r($data,1).PHP_EOL,FILE_APPEND);
	
		$hashValidated = __('Success');

        $txnResponseCode = $this->null2unknown($data["rra_trnscode"]);
        $orderStatus = Order::STATE_PROCESSING;
        $this->getCoreSession()->unsMessages();
        // if($this->getData("rra_trnscode")=="0")
        if(!$errorExists && $this->getData("rra_trnscode") == "0" && $this->_getOrder()->getState() == Order::STATE_PROCESSING)
		{
            $this->_logger->error(sprintf('OrderStatus: %s', 'Get txnResponseCode => '.$txnResponseCode.'; Order::STATE_PROCESSING =>'.$orderStatus.' _getOrder()->getState() =>'. $this->_getOrder()->getState()));
            // $this->process_rrabenefits();
            $this->_registerPaymentCapture();	
		}
		else
		{
            $this->_logger->error(sprintf('OrderStatus else: %s', 'Get txnResponseCode => '.$txnResponseCode.'; Order::STATE_PROCESSING =>'.$orderStatus.' _getOrder()->getState() =>'. $this->_getOrder()->getState()));
			$errMsg = $this->getResponseDescription($txnResponseCode);
			$this->updatePayment(true,Order::STATE_NEW);
			$this->_getOrder()->addStatusHistoryComment($errMsg);
			$this->_getOrder()->save();
			$orderViewURL = $this->_urlInterface->getUrl('sales/order/view',['order_id' => $this->_getOrder()->getId()]);
            
            // $this->_objectManager->get('\Magento\Framework\Message\ManagerInterface')->addError(sprintf($errMsg.'.<br/>Order number: <a href="%s" alt="">%s</a>.', $orderViewURL, $this->getData('rra_ordernumber')));
			
			throw new \Exception($hashValidated);
		}		
    }
    public function getfullUrl(){
        $urlInterface = $this->_objectManager->get('Magento\Framework\UrlInterface');
        return $urlInterface->getCurrentUrl();
    }
    public function updatePayment($isPending=false,$orderStatus=Order::STATE_PROCESSING){
        $payment = $this->_getOrder()->getPayment();
        $payment->setTransactionId($this->getData('rra_trnsnumber'))
                ->setParentTransactionId($this->getData('rra_ordernumber'))
                ->setShouldCloseParentTransaction(false)
                ->setIsTransactionClosed(false)
                ->setAdditionalInformation('TransactionNo:'.$this->getData('rra_trnsnumber').PHP_EOL.'ReceiptNo:'.$this->getData('rra_ordernumber').PHP_EOL.'Message:'.$this->getData('rra_Message'))
                ->setStatus($orderStatus)
                ->setIsTransactionPending($isPending);
    }
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
            $invoice = $this->_getOrder()->prepareInvoice();
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
            $this->_getOrder()->addStatusHistoryComment('Health Credits Payment Secure: Exception occurred during _registerPaymentCapture() action. Exception message: '.$e->getMessage(), false);
            throw $e;
        }
        
    }
    function InvoiceSender($invoice){
        $invoiceSender = $this->_objectManager->get('Magento\Sales\Model\Order\Email\Sender\InvoiceSender');
        $invoiceSender->send($invoice);
        return true;
    }
    public function getResponseDescription($responseCode) {
		
	    switch ($responseCode) {
	        case "0" : $result = "Transaction Successful"; break;
	        case "?" : $result = "Transaction status is Failed"; break;
	    }
	    return $result;
	}

    protected function _getConnection()
    {
        $this->_resource = $this->_objectManager->get("\Magento\Framework\App\ResourceConnection");
        $this->connection = $this->_resource->getConnection('core_write');
        return $this->connection;
    }
	public function null2unknown($data) {
	    if ($data == "") {
	        return "No Value Returned";
	    } else {
	        return $data;
	    }
    }
    public function getOrder() {
		$orderIncrementId = $this->getCheckoutSession()->getLastRealOrderId();
        $order = $this->_objectManager->get('Magento\Sales\Model\Order')->loadByIncrementId($orderIncrementId);		
		if($order->getId()){
			$this->_order = $order;
		}else{
            throw new \LogicException('Sales order object not found. Or You do not have an Internet Connection.');
		}
		
		return $this->_order;
    }
}