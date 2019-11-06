<?php
 /**
 * @category  Unilab
 * @package   Unilab_Movshipping
 * @author    Kristian Claridad
 */
namespace Unilab\Movshipping\Model\Carrier;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Config;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Psr\Log\LoggerInterface;
use Magento\Customer\Model\Session as CustomerSession;

class Movshipping extends AbstractCarrier implements CarrierInterface
{

	protected $_code = 'minimumordervalue';
	protected $_isFixed = true;
	protected $_rateResultFactory;
	protected $_rateMethodFactory;
	protected $customerSession;
	protected $connection;
	protected $_checkoutSession;
	protected $_cartSession;


	public function __construct(
		ScopeConfigInterface $scopeConfig,
		ErrorFactory $rateErrorFactory,
		LoggerInterface $logger,
		ResultFactory $rateResultFactory,
		MethodFactory $rateMethodFactory,
		CustomerSession $customerSession,
		\Magento\Framework\App\ResourceConnection $resource,
		\Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Checkout\Model\Cart $cartSession,
		array $data = []
	) {
		$this->_rateResultFactory = $rateResultFactory;
		$this->_rateMethodFactory = $rateMethodFactory;
		$this->customerSession = $customerSession;
		$this->_resource = $resource;
		$this->_checkoutSession = $checkoutSession;
		$this->_cartSession = $cartSession;
		parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
	}
	 
	public function getAllowedMethods()
	{
		return [$this->getCarrierCode() => __($this->getConfigData('name'))];
	}

	public function collectRates(RateRequest $request)
	{
		/**
		* Make sure that Shipping method is enabled
		*/
		 
		if (!$this->isActive()) {
		return false;
		}
		 
		$result = $this->_rateResultFactory->create();
		$method = $this->_rateMethodFactory->create();
		/**
		* Set carrier's method data
		*/
		$checkout = $this->_getCheckout();
		$subtotal = $this->_getCartSubTotal();

		//shipping information
		$shippingAddress    = $checkout->getShippingAddress();
        $regionid           = $shippingAddress->getRegionId();
        $cityname           = trim($shippingAddress->getCity());
		
		$cityid = $this->_getCityId($regionid, $cityname);
		
		$groups  = $this->getConfigData('destinations');
		$expgroup = explode(",",$groups);

		foreach ($expgroup as $key){
			$groupid = $key;
			$rows_ = $this->_getMovListofCities($groupid);
			
            $citylist = $rows_['listofcities'];
			$citie = explode(",",$citylist);
			
            if(in_array($cityid, $citie)){

                $mov_group_id           = $rows_['id'];
                $mov_group_name         = $rows_['group_name'];
				$fetchdata				= $this->_getMovGreaterAndLessthan($mov_group_id);
				
                $greater_equal          = $fetchdata['greaterequal_mov'];
                $lessthan_equal         = $fetchdata['lessthan_mov'];
                $minimumorder_value 	= $this->getConfigData('minorderval');
				$carriertitle           = $this->getConfigData('title');

				$freeshipping = $this->freeshipping();
				$skufreeshipping = $this->skufreeshipping();
				
				if($subtotal >= $minimumorder_value){
					//free shipping title if (0.0)
					if($greater_equal == 0 || $freeshipping == true || $skufreeshipping == true){
						// $carriertitle = "Free Shipping";
						$mov_group_name = "Free";
					}
					$shippingFee = $this->getAddedWeight($greater_equal);

					if($freeshipping == true  || $skufreeshipping == true){
						$shippingFee = 0;
					}
					
                    $method->setCarrier($this->getCarrierCode());
                    $method->setCarrierTitle($carriertitle);
                    $method->setMethod($mov_group_name);
                    $method->setMethodTitle($mov_group_name);
                    $method->setPrice($shippingFee);
                    $method->setCost($shippingFee);
                    $result->append($method);
				}else{
					if($freeshipping == true  || $skufreeshipping == true){
							// $carriertitle = "Free Shipping";
							$mov_group_name = "Free";
					}
					
					$shippingFee = $this->getAddedWeight($lessthan_equal);
					if($freeshipping == true  || $skufreeshipping == true){
						$shippingFee = 0;
					}
                    $method->setCarrier($this->getCarrierCode());
                    $method->setCarrierTitle($carriertitle);
                    $method->setMethod($mov_group_name);
                    $method->setMethodTitle($mov_group_name);
                    $method->setPrice($shippingFee);
                    $method->setCost($shippingFee);
                    $result->append($method);
				}
			}
		}
		
		return $result;
	}
	public function getCustomerId()
    {
        return $this->customerSession->getId();
    }
    public function _getCustomerGroupId()
    {
        return $this->customerSession->getCustomerGroupId();
    }
    protected function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->_resource->getConnection('core_write');
        }
        return $this->connection;
    }
    public function _getCityId($regionid, $cityname)
    {
        $query = "SELECT city_id FROM  `unilab_cities` WHERE region_id = '$regionid' AND name = '$cityname'";
        $rows  = $this->getConnection()->fetchRow($query);
        return $rows['city_id'];
    }
    public function _getMovListofCities($groupid){
    	$query = "SELECT * FROM  `unilab_mov_shipping` WHERE id = '$groupid'";
        $row   = $this->getConnection()->fetchRow($query);
        return $row;
    }
    public function _getMovGreaterAndLessthan($mov_group_id){
    	$mov_query  = "SELECT greaterequal_mov, lessthan_mov FROM `unilab_mov_shipping` WHERE id = '$mov_group_id'";
        $fetchdata  = $this->getConnection()->fetchRow($mov_query);
        return $fetchdata;
    }
    public function freeshipping()	
	{		
		$freeshipping = false;
		$freeshipping_customergroups = $this->getConfigData('customer_groups');
		$customergroupsArr = explode(",", $freeshipping_customergroups);
		$customerGroupId = $this->_getCustomerGroupId();
		if (in_array($customerGroupId, $customergroupsArr))
		{			
			$freeshipping = true;
		}				
		
		return $freeshipping;
	}
	public function skufreeshipping()
	{
		$freeshipping = false;
		$validitity_fromdate 	= strtotime($this->_scopeConfig()->getValue('carriers/freeshipping/fromdate'));
		$validitity_todate 		= strtotime($this->_scopeConfig()->getValue('carriers/freeshipping/todate'));
		
		$current_date           = strtotime(date("m/j/Y"));
		
		$skus = $this->_scopeConfig()->getValue('carriers/freeshipping/skufree');
		$skuArr = explode(",", $skus);
		
		if($current_date>=$validitity_fromdate and $current_date<=$validitity_todate)
		{
			$cartItems = $this->_getCheckout()->getAllItems();
			
			foreach ($cartItems as $item)
			{
				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
				$product = $this->_createCatalogProducts()->load($item->getProductId());
				
				$sku =  $product->getSku();
				
				if (in_array($sku, $skuArr))
				{
					$freeshipping = true;
					$test .=$sku.',';
				}
			} 
		}
		
		return $freeshipping;
	}
	public function getAddedWeight($greater_equal)	
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->get('\Magento\Checkout\Helper\Cart');
        $Cartitems = $helper->getCart()->getItems();
        $dimensiontotal = 0;
        foreach ($Cartitems as $item)
		{
            $_prodcut 		= "";
            $itemProductId 	= $item->getProductId();
            $_product 		= $this->_createCatalogProducts()->loadByAttribute('entity_id', $itemProductId);
            $final_Weight 	= $_product->getWeight();
            $getFinalWeight = $final_Weight / 1000;
            $dimensionval 	= $getFinalWeight * $item->getQty();
            $dimensiontotal = $dimensiontotal +  $dimensionval;
        }	
		
        $minimumWeight 	= $this->getConfigData('minweight');
		$priceperkilo 	= $this->getConfigData('priceperkilo');
        $addedWeight 	= $dimensiontotal - $minimumWeight;
		$excessweight 	= explode('.', $addedWeight);
		$excess 		= intval(@$excessweight[0]);
		//$decimal 		= intval($excessweight[1]);
		$additionalshippingfee = $priceperkilo * $excess;
		
		// if($decimal > 0)
		// {
		// 	$additionalshippingfee = $additionalshippingfee + $priceperkilo;
		// }
		
        if($greater_equal == 0)
		{
            $totalShippingFee = 0;
			
        }else{
			
            if($dimensiontotal > $minimumWeight)
			{
				$totalShippingFee = $greater_equal + $additionalshippingfee;
            }else{
                $totalShippingFee = $greater_equal;
            }
        }
        return $totalShippingFee;
    }		
	public function _scopeConfig(){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		return $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
	}
	public function _createCatalogProducts(){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		return $objectManager->create('Magento\Catalog\Model\Product');
	}

    protected function _getCheckout()
    {
        return $this->_checkoutSession->getQuote();
    }
    protected function _getCartSubTotal()
    {
        $carts = $this->_cartSession->getQuote()->getTotals();
        return $carts["subtotal"]->getValue();
    }
}