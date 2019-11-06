<?php
/**
 * BpiSecurepay View XML.
 * @category  Unilab
 * @package   BpiSecurepay
 * @author    Kristian Claridad
 */
namespace Unilab\BpiSecurepay\Model\Method;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use \Magento\Sales\Model\Order;
class Payment extends \Magento\Payment\Model\Method\AbstractMethod
{
   	protected $_canSaveCc 				= true;
    protected $_isGateway               = true;
    protected $_canAuthorize            = false;		// Auth Only
    protected $_canCapture              = true;	    // Sale, Capture
    protected $_canCapturePartial       = true;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;     // Creation of a transaction from the admin panel
    protected $_canUseCheckout          = true;
	protected $_canFetchTransactionInfo = true;
	
	protected $_isInitializeNeeded 		= true;
	protected $_code 					= 'bpisecurepay'; // NOTE: this should also be the code name in system.xml and config.xml
	
	protected $_formBlockType 			= 'Unilab\BpiSecurepay\Block\Form';
	protected $_infoBlockType 			= 'Unilab\BpiSecurepay\Block\Info';
	
	protected $orderRepository;
	protected $_order;
	public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->priceCurrency = $priceCurrency;
      }
	
	public function getCheckout() {
		return $this->_objectManager->get('Magento\Customer\Model\Session');
	}
	
	public function getConfig(){
		return $this->_objectManager->get('\Unilab\BpiSecurepay\Model\Config')->getPaymentConfig();
	}
	public function getReturnUrl() {
		return $this->base_url() . "bpisecurepay/payment/response";
	}
	public function base_url(){
		$storeManager = $this->_objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		return $storeManager->getStore()->getBaseUrl();

	}
	public function getLastRealOrder()
	 {
	    $orderId = $this->getLastRealOrderId();
	    if ($this->_order !== null && $orderId == $this->_order->getIncrementId()) {
	        return $this->_order;
	    }
	    $this->_order = $this->_orderFactory->create();
	    if ($orderId) {
	        $this->_order->loadByIncrementId($orderId);
	    }
	    return $this->_order;
	}
	
	public function getcustomOrder() {
		return $this->_objectManager->get('Unilab\BpiSecurepay\Controller\Payment')->getcustomOrder();
	}
	public function getCheckoutFormFields() {
		return $this->getPaymentCheckoutRequest();
	}
	public function getPaymentCheckoutRequest() {

		$orderIncrementId = $this->getCheckout()->getLastRealOrderId();
		$conf = array();
		$conf['vpc_Merchant']		= $this->getConfig()->getMerchant();
		$conf['vpc_AccessCode']		= $this->getConfig()->getMerchantAccesscode();
		$conf['vpc_Version']		= $this->getConfig()->getVpcVersion();
		$conf['vpc_Command']		= $this->getConfig()->getVpcCommand();
		$conf['vpc_MerchTxnRef']	= $this->getcustomOrder()->getIncrementId();
		$conf['vpc_Amount']			= (string) $this->getAmount();
		$conf['vpc_Locale']			= 'en';
		$conf['vpc_ReturnURL']		= $this->getReturnUrl();
		$conf['vpc_VirtualPaymentClientURL']= $this->getConfig()->getPaymentUrl();
		
		file_put_contents('./debug-bpi-payment.txt', print_r($conf,1).PHP_EOL,FILE_APPEND);
		
		return $conf;
	}
	public function getAmount() {
		$amount = sprintf("%.2f",$this->getcustomOrder()->getGrandTotal());	
		if($this->getConfig()->getEnableTestmode()){
			return str_replace(array('.',','),'',$amount);
		}
		return $amount; 
	}
}