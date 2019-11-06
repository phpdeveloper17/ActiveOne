<?php
/**
 * DragonPay View XML.
 * @category  Unilab
 * @package   Unilab_DragonPay
 * @author    Kristian Claridad
 */
namespace Unilab\DragonPay\Model\Method;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use \Magento\Sales\Model\Order;
class Standard extends \Magento\Payment\Model\Method\AbstractMethod
{
   
    protected $_code                    = 'dragonpay';
    // protected $_formBlockType           = 'Unilab\DragonPay\Block\Form';
    protected $_config = null;


    protected $_isGateway               = true;
    protected $_canAuthorize            = false;    ## Auth Only
    protected $_canCapture              = true;     ## Sale, Capture
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;     ## Creation of a transaction from the admin panel
    protected $_canUseCheckout          = true;

	
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
        return $this->_objectManager->create('\Unilab\DragonPay\Model\Session');
    }
    public function getCheckout()
    {
        return $this->_objectManager->create('\Magento\Checkout\Model\Session');
    }
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }
    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        /*
         * TODO: Send Billing Information
         */
        return $this;
    }

    public function getStandardCheckoutFormFields()
    {
        $orderIncrementId = $this->getCheckout()->getLastRealOrderId();
        $order = $this->_objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($orderIncrementId);
		if ($order->getId()) {
                // add order information to the session
                $this->getCheckout()
                    ->setLastOrderId($order->getId())
                    ->setLastRealOrderId($order->getIncrementId())
                    ->setLastOrderStatus($order->getStatus());
		}
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($orderIncrementId);
        $result = array();
        $result["merchantid"] = $this->_scopeConfig->getValue('payment/dragonpay/merchantid');
        $result["txnid"] =  $orderIncrementId;
        $result["amount"] = sprintf("%.2f",$order->getGrandTotal());
        $result["ccy"] = $order->getBaseCurrencyCode();
        $result["description"] = $this->getOrderDescription($order);
        $result["email"] = $order->getCustomerEmail();

        $tmpResult = $result;
        $result["digest"] = $this->getSHA1Digest($tmpResult);

        file_put_contents('./debug-dragon-payment.txt', print_r($result,1).PHP_EOL,FILE_APPEND);
        
        return $result;
    }

    public function getSHA1Digest($params=array()){
        $digester=array();
        foreach($params as $value){
            $digester[] = $value; //.self::SHA1_PREFIX;
        }
        $digester[] = $this->_scopeConfig->getValue('payment/dragonpay/merchantpaswd');
        $merge = implode(":",$digester);
        return sha1($merge);
    }
    public function getOrderDescription($order){
        
        $orderUrl = $this->getUrl('*/sales_order/view',array('order_id' => $order->getId()));
        $message = sprintf( 'Payment from '.$this->_scopeConfig->getValue('general/store_information/name').'. Order Ref#:%s',$order->getIncrementId());
        $description = $this->_scopeConfig->getValue('payment/dragonpay/payment_description');
        if(empty($description)){
            $description = $message;
        }
        return $description;
    }
    public function getCurrentStoreId() {
        return $this->_objectManager->get('\Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
    }
}