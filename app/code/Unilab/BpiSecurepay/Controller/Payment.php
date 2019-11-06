<?php
/**
 * BpiSecurepay View XML.
 * @category  Unilab
 * @package   BpiSecurepay
 * @author    Kristian Claridad
 */
namespace Unilab\BpiSecurepay\Controller;

use \Magento\Framework\Validator\Exception;

class Payment extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    protected $_quoteRepository;
    protected $_logger;
    protected $_checkoutSession;
    protected $_bpiSecurepayConfig;
    protected $_bpiSecurepayApi;
    protected $_registry;

    /**
     * @param \Magento\Framework\App\Action\Context                        $context
     * @param \Magento\Framework\View\Result\PageFactory resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Psr\Log\LoggerInterface $loggerInteface,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Unilab\BpiSecurepay\Model\Config $bpiSecurepayConfig,
        \Unilab\BpiSecurepay\Model\Api $bpiSecurepayApi,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);

        $this->_logger = $loggerInteface;
        $this->_messageManager = $context->getMessageManager();
        $this->_quoteRepository = $cartRepositoryInterface;
        $this->_checkoutSession = $checkoutSession;
        $this->_bpiSecurepayConfig = $bpiSecurepayConfig;
        $this->_bpiSecurepayApi = $bpiSecurepayApi;
        $this->_registry = $registry;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }
    
    public function execute()
    {
        return $this->resultPageFactory->create();
    }
    protected function _expireAjax()
    {
        if (!$this->_getCheckout()->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }
    protected function redirectAction()
    {
        $layout = $this->_view->getLayout();
    	
        $this->_getCheckout()->setPaymentQuoteId($this->_getCheckout()->getQuoteId());
       	$this->getResponse()->setBody($layout->createBlock('Unilab\BpiSecurepay\Block\Redirect')->toHtml());
        $this->_getCheckout()->unsQuoteId();
        $this->_getCheckout()->unsRedirectUrl();

    }
    
	function base_url(){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		return $storeManager->getStore()->getBaseUrl();

	}
    protected function _404()
    {
        $this->_registry->register('bpisecurepay_forward_nocache', true);
        $this->_forward('defaultNoRoute');
    }

    protected function _loadQuoteFromOrder(\Magento\Sales\Model\Order $order)
    {
        $quoteId = $order->getQuoteId();

        // Retrieves quote
        $quote = $this->_quoteRepository->get($quoteId);
        if (empty($quote) || null === $quote->getId()) {
            $message = 'Not existing quote id associated with the order %d';
            throw new \LogicException(__($message, $order->getId()));
        }

        return $quote;
    }

    /**
     * Get checkout session namespace.
     *
     * @return \Magento\Checkout\Model\Session
     */
    protected function _getCheckout()
    {
        return $this->_checkoutSession;
    }
    public function logDebug($message)
    {
        $this->_logger->debug($message);
    }
    
    public function getcustomOrder() {
        $orderIncrementId = $this->_getCheckout()->getLastRealOrderId();
        $order = $this->_objectManager->get('Magento\Sales\Model\Order')->loadByIncrementId($orderIncrementId);
        if($order->getId()){
          return $this->_order = $order;
        }else{
             $this->logDebug(sprintf('payment: %s', 'Sales order object not found.'));
        }
        
    }
    // public function cancelAction()
    // {
    //     $this->_getCheckout()->setQuoteId($this->_getCheckout()->getPaymentQuoteId(true));
    //     if ($this->_getCheckout()->getLastRealOrderId()) {
    //         $order = Mage::getModel('sales/order')->loadByIncrementId($this->_getCheckout()->getLastRealOrderId());
    //         if ($order->getId() && ($order->getState() == Mage_Sales_Model_Order::STATE_PROCESSING || $order->getState() == Mage_Sales_Model_Order::STATE_NEW || $order->getState() == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) ) {
    //             $order->cancel()->save();
    //         }
    //     }
    //     $this->_redirect('checkout/cart');
    // }

    public function successAction()
    {        
        $this->_getCheckout()->setQuoteId($this->_getCheckout()->getPaymentQuoteId(true));
        $this->_getCheckout()->getQuote()->setIsActive(false)->save();  
        $this->_redirect('checkout/onepage/success', array('_secure'=>true));
    }   
    
}
