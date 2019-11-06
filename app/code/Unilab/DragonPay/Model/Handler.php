<?php
/**
 * DragonPay View XML.
 * @category  Unilab
 * @package   Unilab_DragonPay
 * @author    Kristian Claridad
 * RRA/Dragonpayapi/Model/Apisuccess.php -> Handler
 */
namespace Unilab\DragonPay\Model;

use \Magento\Sales\Model\Order;
use \Magento\Framework\Validator\Exception;

class Handler extends \Magento\Payment\Model\Method\AbstractMethod {
    
    protected $_order = null;
    protected $responseCode = '';

    protected $_storeManager;
    protected $_redirect;
    protected $response;
    protected $_invoiceService;
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\UrlInterface $urlInterface,
        \Unilab\Dragonpay\Logger\Logger $loggerInteface,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Unilab\Dragonpay\Helper\Data $coreData,
        \Magento\Framework\DB\Transaction $transactionFactory //resource_transaction 
    ) {
        $this->_objectManager = $objectManager;
        $this->_urlInterface = $urlInterface;
        $this->_logger = $loggerInteface;
        $this->_scopeConfig = $scopeConfig;
        $this->_coreHelper = $coreData;
        $this->_transactionFactory = $transactionFactory;

        $this->_storeManager = $this->_objectManager->create('Magento\Store\Model\StoreManagerInterface');
        $this->_redirect = $this->_objectManager->create('\Magento\Framework\App\Response\Http');
        $this->response = $this->_objectManager->create('Magento\Framework\App\ResponseInterface');
        $this->_invoiceService = $this->_objectManager->create('\Magento\Sales\Model\Service\InvoiceService');

    }

    public function getCoreSession(){
        return $this->_objectManager->create('\Magento\Framework\Session\SessionManagerInterface');
    }   
    
    public function getCheckoutSession(){
        return $this->_objectManager->create('\Magento\Checkout\Model\Session');
    }
    
    protected function _getOrder()
    {
        if (empty($this->_order)) {
            $id = $this->getData('ordernumber');
            if(empty($id)):
                $id = $this->getData("txnid");
            endif;
            $this->_order = $this->_objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($id);
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
        $orderStatus=Order::STATE_PROCESSING;
        
        $errMsg = '';
        $status         = $this->getData("status");
        $ordernumber    = $this->getData("txnid");
        $errorExists = false;
        $data = $this->getData();
        $this->getCoreSession()->unsMessages();
        if($status == "S" && $this->_getOrder()->getState() == Order::STATE_PROCESSING)
        {
            
            $this->_logger->error(sprintf('OrderStatus: %s', 'Get orderstatus => '.$status.'; Order::STATE_PROCESSING =>'.$orderStatus.' _getOrder()->getState() =>'. $this->_getOrder()->getState()));

            $this->getCoreSession()->setTransactionNo($this->getData('refno'));
            $payment = $this->_getOrder()->getPayment();
            $payment->setTransactionId($this->getData('txnid'))
            ->setParentTransactionId($this->getData('refno'))
            ->setShouldCloseParentTransaction(false)
            ->setIsTransactionClosed(false)
            ->setAdditionalInformation('TransactionNo: '.$this->getData('txnid').PHP_EOL.'Ref. No: '.$this->getData('refno').PHP_EOL.'Message:'.$this->getData('message'))
            ->setStatus($orderStatus);

            $payment = $this->_getOrder()->getPayment();
            $payment->setPreparedMessage('');
            $this->_getOrder()->save();

            if(!$this->_getOrder()->canInvoice())
            {
                throw new \Exception('Cannot create an invoice.');
            }

            $invoice = $this->_getOrder()->prepareInvoice();

            if (!$invoice->getTotalQty()) {
                throw new \Exception('Cannot create an invoice without products');
            }
             
            $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
            $invoice->register();
            
            $transactionSave = $this->_transactionFactory->addObject($invoice)
            ->addObject($invoice->getOrder());
        
            $transactionSave->save();
            
            $this->sendemailto();

            return true;

        }else{
            $this->_logger->error(sprintf('OrderStatus else: %s', 'Get orderstatus => '.$status.'; Order::STATE_PROCESSING =>'.$orderStatus.' _getOrder()->getState() =>'. $this->_getOrder()->getState()));

            $this->changeOrderStatus();
            $this->_getOrder()->addStatusHistoryComment($errMsg);
            $this->_getOrder()->save();
            $orderViewURL = $this->_urlInterface->getUrl('sales/order/view',['order_id' => $this->_getOrder()->getId()]);
            return false;
        }       
    }

    protected function _getConnection()
    {
        $this->_resource = $this->_objectManager->create("\Magento\Framework\App\ResourceConnection");
        $this->connection = $this->_resource->getConnection('core_write');
        return $this->connection;
    }
    
     protected function changeOrderStatus()
     {
        $incrementId        = $this->getData('txnid');
        $order              = $this->_objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($incrementId);
        $entity_id          = $order->getentity_id();
        $fields             = array();
        $fields['status']   = 'payment_verification';
        $where              = array();
        $where[]            = $this->_getConnection()->quoteInto('status =?','pending');
        $where[]            = $this->_getConnection()->quoteInto('entity_id =?',$entity_id);

        $this->_getConnection()->update('sales_order', $fields, $where);
        // $this->_getConnection()->commit();

        $fields             = array();
        $fields['status']   = 'payment_verification';
        $where              = array();
        $where[]            = $this->_getConnection()->quoteInto('entity_id =?',$entity_id);
        $this->_getConnection()->update('sales_order_grid', $fields, $where);
        // $this->_getConnection()->commit();
     }
     
    public function updatePayment($isPending=false,$orderStatus=Order::STATE_PROCESSING){
        $payment = $this->_getOrder()->getPayment();
        $payment->setTransactionId($this->getData('txnid'))
                ->setParentTransactionId($this->getData('refno'))
                ->setShouldCloseParentTransaction(false)
                ->setIsTransactionClosed(false)
                ->setAdditionalInformation('TransactionNo: '.$this->getData('txnid').PHP_EOL.'Ref. No: '.$this->getData('refno').PHP_EOL.'Message:'.$this->getData('message'))
                ->setStatus($orderStatus)
                ->setIsTransactionPending($isPending);
    }

    protected function sendemailto()
    {
        $invoice = $this->_getOrder()->prepareInvoice();
        $invoice->getOrder()->setIsCustomerNotified(true);
        $invoice->getOrder()->setIsInProcess(true);
        $invoice->getOrder()->addStatusHistoryComment(__('Notified customer about invoice.'))
                            ->setIsCustomerNotified(true);
        
        $this->sendEmailInvoice($invoice);             
    }
    function sendEmailInvoice($invoice){
        $invoiceSender = $this->_objectManager->create('Magento\Sales\Model\Order\Email\Sender\InvoiceSender');
        $invoiceSender->send($invoice);
        return true;
    }
}