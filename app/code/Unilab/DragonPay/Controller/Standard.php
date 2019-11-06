<?php
/**
 * DragonPay.
 * @category  Unilab
 * @package   Unilab_DragonPay
 * @author    Kristian Claridad
 */
namespace Unilab\DragonPay\Controller;

use \Magento\Framework\Validator\Exception;
use \Magento\Sales\Model\Order;

class Standard extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    protected $_quoteRepository;
    protected $_logger;
    protected $_checkoutSession;
    protected $_registry;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Unilab\DragonPay\Logger\Logger $loggerInteface,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);

        $this->_logger = $loggerInteface;
        $this->_messageManager = $context->getMessageManager();
        $this->_quoteRepository = $cartRepositoryInterface;
        $this->_checkoutSession = $checkoutSession;
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
    public function redirectAction()
    {
        $layout = $this->_view->getLayout();
        $session = $this->_getCheckout();
        $session->setDragonPayStandardQuoteId($session->getQuoteId());
        $this->getResponse()->setBody($layout->createBlock('Unilab\DragonPay\Block\Redirect')->toHtml());
        $session->unsQuoteId();
        $session->unsRedirectUrl();
    }

    /**
     * When a customer cancel payment from paygate.
     */
    public function cancelAction()
    {
        $session = $this->_getCheckout();
        $session->setQuoteId($session->getDragonPayStandardQuoteId(true));
        if ($session->getLastRealOrderId()) {
            $order = $this->_objectManager->get('Magento\Sales\Model\Order')->loadByIncrementId($session->getLastRealOrderId());
            if ($order->getId() && ($order->getState() == Order::STATE_PROCESSING || $order->getState() == Order::STATE_NEW || $order->getState() == Order::STATE_PENDING_PAYMENT) ) {
                $order->cancel()->save();
            }
        }
        $this->_redirect('checkout/cart');
    }
    
    protected function _404()
    {
        $this->_registry->register('dragonpay_forward_nocache', true);
        $this->_forward('defaultNoRoute');
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

    public function logWarning($message)
    {
        $this->_logger->warning($message);
    }

    public function logError($message)
    {
        $this->_logger->error($message);
    }

    public function logFatal($message)
    {
        $this->_logger->critical($message);
    }
    public function getfullUrl(){
        $urlInterface = $this->_objectManager->get('Magento\Framework\UrlInterface');
        return $urlInterface->getCurrentUrl();
    }
}
