<?php
/**
 * Healthcredits View XML.
 * @category  Unilab
 * @package   Unilab_Healthcredits
 * @author    Kristian Claridad
 */
namespace Unilab\Healthcredits\Model\Method;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use \Magento\Sales\Model\Order;
class Payment extends \Magento\Payment\Model\Method\AbstractMethod
{
   
    protected $_canSaveCc 				= true;
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;		// Auth Only
    protected $_canCapture              = true;	    // Sale, Capture
    protected $_canCapturePartial       = true;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;     // Creation of a transaction from the admin panel
    protected $_canUseCheckout          = true;
	protected $_canFetchTransactionInfo = true;
	protected $_code 					= 'healthcredits'; // NOTE: this should also be the code name in system.xml and config.xml	
	protected $_formBlockType 			= 'Unilab\Healthcredits\Block\Form';
	protected $_infoBlockType 			= 'Unilab\Healthcredits\Block\Info';

	
	protected $orderRepository;
	protected $_order;
    protected $_scopeConfig;
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
        $this->_scopeConfig = $scopeConfig;
    }

    public function getSession()
    {
        return $this->_objectManager->get('\Unilab\Healthcredits\Model\Session');
    }
    public function getCheckout()
    {
        return $this->_objectManager->get('\Magento\Checkout\Model\Session');
    }
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }
    public function getConfig(){
		return $this->_objectManager->get('\Unilab\Healthcredits\Model\Config')->getPaymentConfig();
	}
	public function getReturnUrl() {
		return $this->base_url() . "healthcredits/payment/response";
	}
	public function base_url(){
		$storeManager = $this->_objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		return $storeManager->getStore()->getBaseUrl();

	}
    public function getOrder() {
		$orderIncrementId = $this->getCheckout()->getLastRealOrderId();
        $order = $this->_objectManager->get('Magento\Sales\Model\Order')->loadByIncrementId($orderIncrementId);		
		if($order->getId()){
			$this->_order = $order;
		}else{
            throw new \LogicException('Sales order object not found. Or You do not have an Internet Connection.');
		}
		
		return $this->_order;
    }
    public function getCheckoutFormFields() {
		return $this->getPaymentCheckoutRequest();
	}
	public function getPaymentCheckoutRequest() {

		$conf = array();
		$conf['rra_access_code']		= $this->getConfig()->getMerchantAccesscode();
		$conf['rra_ordernumber']		= $this->getOrder()->getIncrementId();		
		$conf['rra_amount']				= (string) $this->getAmount();
		$conf['rra_returnurl']			= $this->getReturnUrl();
		$conf['rra_storevirualurl']		= $this->getConfig()->getPaymentUrl();	
		
		file_put_contents('./debug-healthcredits-payment.txt', print_r($conf,1).PHP_EOL,FILE_APPEND);
		
		return $conf;
    }
    public function getAmount() {
        $amount = 0;	
		$amount = sprintf("%.2f",$this->getOrder()->getGrandTotal());	
		return $amount; 
	}
}