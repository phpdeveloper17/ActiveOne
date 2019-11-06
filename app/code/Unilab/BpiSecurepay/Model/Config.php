<?php
/**
 * BpiSecurepay View XML.
 * @category  Unilab
 * @package   Unilab_BpiSecurepay
 * @author    Kristian Claridad
 */
namespace Unilab\BpiSecurepay\Model;

class Config extends \Magento\Payment\Model\Config {
	
	const _MEGS_PAYMENT_URL = 'https://migs.mastercard.com.au/vpcpay';
	const _MEGS_VPC_VERSION = 1;
	const _MEGS_VPC_COMMAND = 'pay';
	
	const _PAYMENT_DESCRIPTION = 'Payment from unilab onlinestore (Ref:%s)';
	const CODE = 'bpisecurepay';
	
	protected $_transactionTypes = array(1 => 'Sales', 2 => 'Authorize');
	protected $_ipayment_config = 'payment/bpisecurepay/';

	protected $_dataStorage;
    protected $_scopeConfig;
    protected $_objectManager;
    private $_store;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Factory $paymentMethodFactory,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\Config\DataInterface $dataStorage,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($scopeConfig, $paymentMethodFactory, $localeResolver, $dataStorage, $date);
        $this->_dataStorage = $dataStorage;
        $this->_scopeConfig = $scopeConfig;
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
    }
	
	
	public function getPaymentConfig() {
		$configs = array(	'active',
							'merchant_live', 
							'merchant_test', 
							'merchant_accesscode_live',
							'merchant_accesscode_test',
							'merchant_secure_hash_secret_test',
							'merchant_secure_hash_secret_live',
							'enable_testmode',
						);
		

		$settings = new \Magento\Framework\DataObject();
		$settings->setItem($configs);
		foreach ($configs as $config_key) {
			$settings->setData($config_key, $this->getStoreConfig($config_key));
		}
		
		// //auto set URL, username, password
		if (!$settings->getEnableTestmode()) {
			$settings->setMerchant($settings->getMerchantLive()) 
					->setMerchantAccesscode($settings->getMerchantAccesscodeLive())
					->setMerchantSecureHashSecret($settings->getMerchantSecureHashSecretLive())
					;
		} else {			
			$settings->setMerchant($settings->getMerchantTest()) 
					 ->setMerchantAccesscode($settings->getMerchantAccesscodeTest())
					 ->setMerchantSecureHashSecret($settings->getMerchantSecureHashSecretTest())
					 ;
		}
		
		$settings->setPaymentUrl(self::_MEGS_PAYMENT_URL);
		$settings->setVpcVersion(self::_MEGS_VPC_VERSION);
		$settings->setVpcCommand(self::_MEGS_VPC_COMMAND);
		
        return $settings;
	}

	public function getCurrentStoreId() {
		return $this->_storeManager->getStore()->getId();
	}

	protected function getStoreConfig($config_id) {
		return $this->_getConfigValue($this->_ipayment_config . $config_id,$this->getCurrentStoreId());
	}
	private function _getConfigValue($name)
    {
        return $this->_scopeConfig->getValue($name, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
	
}
	