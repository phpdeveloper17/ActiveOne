<?php
/**
 * DragonPay Handler
 * @category  Unilab
 * @package   Unilab_DragonPay
 * @author    Kristian Claridad
 */
namespace Unilab\DragonPay\Model;

use Magento\Sales\Model\Order;
use \Magento\Framework\Validator\Exception;

class Handler extends \Magento\Payment\Model\Method\AbstractMethod
{
    const PAYMENT_STATUS_SUCCESS = "completed";
    /*
    * @param Mage_Sales_Model_Order
    */
    protected $_order = null;

    /**
     *
     * @var Mage_Paypal_Model_Config
     */
    protected $_config = null;

    /**
     * PayPal info instance
     *
     * @var Mage_Paypal_Model_Info
     */
    protected $_info = null;

    /**
     * IPN request data
     * @var array
     */
    protected $_request = array();

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\UrlInterface $urlInterface,
        \Unilab\BpiSecurepay\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Unilab\BpiSecurepay\Helper\Data $coreData,
        \Magento\Framework\DB\Transaction $transactionFactory //resource_transaction 
    ) {
        $this->_objectManager = $objectManager;
        $this->_urlInterface = $urlInterface;
        $this->_helper = $helper;
        $this->_logger = $logger;
        $this->_scopeConfig = $scopeConfig;
        $this->_coreHelper = $coreData;
        $this->_transactionFactory = $transactionFactory;

        $this->_storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $this->_redirect = $this->_objectManager->get('\Magento\Framework\App\Response\Http');
    }
    /**
     * postback/returned request data getter
     *
     * @param string $key
     * @return array|string
     */
    public function getRequestData($key = null)
    {
        if (null === $key) {
            return $this->_request;
        }
        return isset($this->_request[$key]) ? $this->_request[$key] : null;
    }

    /**
     * Get data from dragonpay and validate
     *
     * @param array $request
     * @throws Exception
     */
    public function processRequest(array $request)
    {
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . ":$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $datarequest = $request;
        $datarequest['fullurl'] = $actual_link;

        file_put_contents('./debug-dragon-payment.txt', print_r($datarequest,1).PHP_EOL,FILE_APPEND);

        $this->_request   = $request;
        $forDigest = $this->_request;
        unset($forDigest['digest']);

        $computedSha1 = $this->_objectManager->get('\Unilab\DragonPay\Model\Method\Standard')->getSHA1Digest($forDigest);

        // We redirect directly if failure
        
        if (count($this->_request['digest'])>0 && $computedSha1 != $this->_request['digest'])
        {
            throw new \Exception("Invalid Postback Data");
        }
        else
        {
            try
            {
                $this->_getOrder();
                $this->_processOrder(); // TODO: Handle all type of dragonpay status and action

                // We redirect directly if failure
                if($this->_request['status'] == \Unilab\DragonPay\Model\Info::STATUS_FAILURE){
                    $this->_objectManager->get('\Magento\Framework\Message\ManagerInterface')->addError('DragonPay Error: '.$this->_request['message']);
                        $this->_redirect->setRedirect('cancel');
                }else{
                        $this->_redirect->setRedirect('success');
                }

            }
            catch(Exception $e){
                throw $e;
            }
        }
    }


    /**
     * Load and validate order, instantiate proper configuration
     *
     *
     * @return Mage_Sales_Model_Order
     * @throws Exception
     */
    protected function _getOrder()
    {
        if (empty($this->_order)) {
            // get proper order
            $id = $_GET['ordernumber'];
            if(empty($id)):
                $id = $_GET['txnid'];
            endif;
            $this->_order = $this->_objectManager->get('Magento\Sales\Model\Order')->loadByIncrementId($id);
            $this->response = $this->_objectManager->get('Magento\Framework\App\ResponseInterface');
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

    /**
     * DragonPay Postback workflow implementation
     */
    protected function _processOrder()
    {
        $this->_order = null;
        $this->_getOrder();

        try {
            // handle payment_status
            $paymentStatus = $this->_request['status'];

            switch ($paymentStatus) {
                // paid
                case \Unilab\DragonPay\Model\Info::STATUS_SUCCESS:
                    $this->_registerPaymentCapture();
                    break;
					
				case \Unilab\DragonPay\Model\Info::STATUS_PENDING:
					//$this->_registerPaymentCapture();
                    break;
                default:
                    throw new \Exception("Cannot handle payment status '{$paymentStatus}'.");
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Process completed payment (either full or partial)
     */
    protected function _registerPaymentCapture()
    {
       
        $payment = $this->_order->getPayment();

        if ($this->_order->getState() == Order::STATE_PROCESSING) {

            $payment->setTransactionId($this->getRequestData('txnid'))
                    ->setParentTransactionId($this->getRequestData('refno'))
                    ->setShouldCloseParentTransaction(false)
                    ->setIsTransactionClosed(false)
                    ->setAdditionalInformation($this->getRequestData('message'))
                    ->setStatus(Order::STATE_PROCESSING)
                    ->setIsTransactionPending(false)
                    ->setPreparedMessage($this->_createIpnComment(''));

            $this->_order->setDragonpayRefno($this->getRequestData('refno'));
            $this->_order->save();
			
            try {
            	
                if(!$this->_getOrder()->canInvoice())
	            {
	                throw new \LogicException('Cannot create an invoice.');
	            }
	            $invoice = $this->_objectManager->get('\Magento\Sales\Model\Service\InvoiceService', $this->_getOrder())->prepareInvoice();
	            if (!$invoice->getTotalQty()) {
	                throw new \LogicException('Cannot create an invoice without products');
	            }
                $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
				$invoice->register();
				
				$invoice->getOrder()->setIsCustomerNotified(true);
				$invoice->getOrder()->setIsInProcess(true);
				$invoice->getOrder()->addStatusHistoryComment(
	                        	$this->_coreHelper->__('Notified customer about invoice.')
	                                )->setIsCustomerNotified(true);
				$invoice->sendEmail();
					
				$transactionSave = $this->_transactionFactory->addObject($invoice)
                ->addObject($invoice->getOrder());
            
                $transactionSave->save();
                
                $this->_eventManager->dispatch('unilab_payment_captured_after', ['order' => $this->_getOrder()]);
							
				
            }
            catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->_getOrder()->addStatusHistoryComment('DragonPay: Exception occurred during _registerPaymentCapture() action. Exception message: '.$e->getMessage(), false);
                $this->_order->cancel()->save();
            }
        }
    }

    /**
     * Generate an additional explanation.
     * Returns the generated comment or order status history object
     *
     * @param string $comment
     * @param bool $addToHistory
     * @return string|Mage_Sales_Model_Order_Status_History
     */
    protected function _createIpnComment($comment = '', $addToHistory = false)
    {
        $paymentStatus = $this->getRequestData('message');
        $message = $this->_coreHelper->__('%s.', $paymentStatus);
        if ($comment) {
            $message .= ' ' . $comment;
        }
        if ($addToHistory) {
            $message = $this->_order->addStatusHistoryComment($message);
            $message->setIsCustomerNotified(null);
        }
        return $message;
    }
}