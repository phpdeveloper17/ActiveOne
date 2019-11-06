<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Catalog\Model\Product\Type;

use Magento\Catalog\Model\Product;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\Store;
use Magento\Catalog\Api\Data\ProductTierPriceExtensionFactory;
use Magento\Framework\App\ObjectManager;

/**
 * Product type price model
 *
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.2
 */
class Price extends \Magento\Catalog\Model\Product\Type\Price
{
    /**
     * Product price cache tag
     */
    const CACHE_TAG = 'PRODUCT_PRICE';

    /**
     * @var array
     */
    protected static $attributeCache = [];

    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Rule factory
     *
     * @var \Magento\CatalogRule\Model\ResourceModel\RuleFactory
     */
    protected $_ruleFactory;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var GroupManagementInterface
     */
    protected $_groupManagement;

    /**
     * @var \Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory
     */
    protected $tierPriceFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $config;

    /**
     * @var ProductTierPriceExtensionFactory
     */
    private $tierPriceExtensionFactory;

    /**
     * Constructor
     *
     * @param \Magento\CatalogRule\Model\ResourceModel\RuleFactory $ruleFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param GroupManagementInterface $groupManagement
     * @param \Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory $tierPriceFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param ProductTierPriceExtensionFactory|null $tierPriceExtensionFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\CatalogRule\Model\ResourceModel\RuleFactory $ruleFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        PriceCurrencyInterface $priceCurrency,
        GroupManagementInterface $groupManagement,
        \Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory $tierPriceFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        ProductTierPriceExtensionFactory $tierPriceExtensionFactory = null,
        \Magento\Checkout\Model\Session  $checkoutSession,
        \Magento\Weee\Model\Config $weeeConfig,
        \Magento\Weee\Model\Tax $weeeTax
    ) {
        $this->_ruleFactory = $ruleFactory;
        $this->_storeManager = $storeManager;
        $this->_localeDate = $localeDate;
        $this->_customerSession = $customerSession;
        $this->_eventManager = $eventManager;
        $this->priceCurrency = $priceCurrency;
        $this->_groupManagement = $groupManagement;
        $this->tierPriceFactory = $tierPriceFactory;
        $this->config = $config;
        $this->tierPriceExtensionFactory = $tierPriceExtensionFactory ?: ObjectManager::getInstance()
            ->get(ProductTierPriceExtensionFactory::class);
        $this->checkoutSession = $checkoutSession;
        $this->_weeeConfig = $weeeConfig;
        $this->_weeeTax = $weeeTax;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * Default action to get price of product
     *
     * @param Product $product
     * @return float
     */
    public function getPrice($product)
    {
        // return 13;
        $_priceHelper = $this->_objectManager->create('Magento\Framework\Pricing\Helper\Data');
        $_weeeHelper = $this->_objectManager->create('Magento\Weee\Helper\Data');
        $_taxHelper  = $this->_objectManager->create('Magento\Tax\Helper\Data');
        $_catalogHelper = $this->_objectManager->create('Magento\Catalog\Helper\Data');
        $_helper = $this->_objectManager->create('Magento\Catalog\Helper\Output');

        $_product = $product;
        $_storeId = $_product->getStoreId();

        $_id = $_product->getId();
        $_weeeSeparator = '';
        $_simplePricesTax = ($_taxHelper->displayPriceIncludingTax() || $_taxHelper->displayBothPrices());
        $_minimalPriceValue = $_product->getMinimalPrice();
        $_minimalPrice = $_catalogHelper->getTaxPrice($_product, $_minimalPriceValue, $_simplePricesTax);
        $_specialPriceStoreLabel = $_product->getResource()->getAttribute('special_price')->getStoreLabel();
        if($_product->getTypeID() != 'grouped'):
            $_weeeTaxAmount = $this->getAmountForDisplay($_product);
            if ($_weeeHelper->typeOfDisplay($_product, array(\Magento\Weee\Model\Tax::DISPLAY_INCL_DESCR, \Magento\Weee\Model\Tax::DISPLAY_EXCL_DESCR_INCL, 4)))
            {
                $_weeeTaxAmount = $block->getAmount($_product);
                $_weeeTaxAttributes = $_weeeHelper->getProductWeeeAttributesForDisplay($_product);
            }
            $_weeeTaxAmountInclTaxes = $_weeeTaxAmount;
            if($_weeeHelper->isTaxable() && !$_taxHelper->priceIncludesTax($_storeId)){
                $_attributes = $_weeeHelper->getProductWeeeAttributesForRenderer($_product, null, null, null, true);
                $_weeeTaxAmountInclTaxes = $_weeeHelper->getAmountInclTaxes($_attributes);
            }
            $customer         = $this->_objectManager->create("\Magento\Customer\Model\Session")->getCustomer();
            // $_price = $_catalogHelper->getTaxPrice($_product, $_product->getPrice());
            // $_regularPrice = $_catalogHelper->getTaxPrice($_product, $_product->getPrice(), $_simplePricesTax);
            // $_finalPrice = $_catalogHelper->getTaxPrice($_product, $_product->getFinalPrice());

            // $customer         = [];
            $_price = 0;
            $_regularPrice = 0;
            $_finalPrice = 0;
            $this->_resource = $this->_objectManager->get("\Magento\Framework\App\ResourceConnection");
            $connection = $this->_resource->getConnection('core_write');

            $rule_price		= null;
            $dnow 			= date("Y-m-d");
            $Tday			= date("l");
            $is_Ctime 		= false;
            $day_isactive 	= false;
            $dateis_active	= false;
            $discount_in_amount  =	0;
            $discount_in_percent =	0;

            $customerLevelID = $customer->getPriceLevel();
            $query 				= "SELECT * FROM catalogrule WHERE '$dnow' BETWEEN from_date AND to_date AND price_level_id='$customerLevelID' AND is_active=1";
            
            $rowCatalogRule 	= $connection->fetchRow($query);
           
            $from_date 			=	$rowCatalogRule ['from_date'];
            $to_date 			=	$rowCatalogRule ['to_date'];
            $limit_days 		=	$rowCatalogRule ['limit_days'];
            $limit_time_from 	=	$rowCatalogRule ['limit_time_from'];
            $limit_time_to 		=	$rowCatalogRule ['limit_time_to'];
            $is_active 			=	$rowCatalogRule ['is_active'];
            $price_level_id 	=	$rowCatalogRule ['price_level_id'];
            $pricelist_id 		=	$rowCatalogRule ['name'];
            $rule_price 		=	$rowCatalogRule ['rule_id'];

            //Search Group ID by Rule ID
            $selectGrpID 	=	$connection->select()->from('catalogrule_customer_group', array('*'))->where('rule_id=?',$rule_price);
            $RwGrpID 	=$connection->fetchAll($selectGrpID);
            $is_group 	= false;
            
            foreach($RwGrpID as $key=>$value):
                if($key == 'customer_group_id'):
                    if($customer->getGroupId() == $value['customer_group_id']):
                        $is_group 	= true;
                    endif;
                endif;
            endforeach;

            if(!empty($from_date) && !empty($to_date)):
                $frm_date 			=	date("Y-m-d", strtotime($from_date));
                $t_date 			=	date("Y-m-d", strtotime($to_date));

                if($dnow >= $frm_date && $dnow <= $t_date):
                    $dateis_active	= true;
                endif;
            else:
                $dateis_active	= true;
            endif;

            //Search rule_price from catalogrule_product_price
            $selectPricelevel 		=	$connection->select()->from('rra_pricelevelmaster', array('*'))->where('id=?',$price_level_id);
            $priceRW 				=	$connection->fetchRow($selectPricelevel);
            $price_level_isactive 	=	$priceRW ['is_active'];
            
            if($price_level_isactive == true):
                if($limit_time_from == '00:00:00' && $limit_time_to == '00:00:00'):
                    $is_Ctime 	= true;
                elseif (time() >= strtotime($limit_time_from) && time() <= strtotime($limit_time_to)):
                    $is_Ctime 	= true;
                endif;
                $limitdays_exp = explode(",",$limit_days);
                
                foreach($limitdays_exp as $_tday):
                    
                    if(strtolower($Tday) == strtolower($_tday)):
                        $day_isactive = true;
                    elseif(strtolower($_tday) == 'everyday'):
                        $day_isactive = true;
                    endif;
                endforeach;
            endif;
            if($is_active == true && $day_isactive == true && $is_Ctime == true && $dateis_active == true):
            
                if(!empty($rule_price)):
                    //Search Unit Price from catalogrule_product_price
                    $selectUnitPrice 	=	$connection->select()->from('rra_pricelistproduct', array('*'))
                    ->where('pricelist_id=?',$pricelist_id)
                    ->where('product_sku=?',$_product->getSku());
                   
                    
                    $UnitpriceRW 		 =	$connection->fetchRow($selectUnitPrice);
                    $unit_price 		 =	$UnitpriceRW ['unit_price'];
                    $discount_in_amount  =	$UnitpriceRW ['discount_in_amount'];
                    $discount_in_percent =	$UnitpriceRW ['discount_in_percent'];
                    $from_date 			 =	$UnitpriceRW ['from_date'];
                    $to_date 			 =	$UnitpriceRW ['to_date'];
                    $from_date			= strtotime($UnitpriceRW['from_date']);
                    $from_date 			= date('Y-m-d',$from_date);
                    $to_date			= strtotime($UnitpriceRW['to_date']);
                    $to_date 			= date('Y-m-d',$to_date);
                    $TodayDate			= date('Y-m-d');
                    
                    if(strtotime($TodayDate)  >= strtotime($from_date) && strtotime($TodayDate)  <= strtotime($to_date)):
                        if(!empty($unit_price) || $unit_price > 0):
                            $final_amount		 = $unit_price;
                            if($discount_in_percent > 0 || !empty($discount_in_percent) ):
                                $discount_percent 	= $unit_price * ($discount_in_percent /100);
                                $final_amount 		= $unit_price - $discount_percent;
                            endif;
                            if($discount_in_amount > 0 || !empty($discount_in_amount) ):
                                $final_amount 		= $unit_price - $discount_in_amount;
                            endif;
                            $_finalPrice 	= $_catalogHelper->getTaxPrice($_product, $final_amount);
                        else:
                            $_finalPrice 	= $_catalogHelper->getTaxPrice($_product, $rule_price);
                        endif;
                    endif;
                    if($_finalPrice <=0):
                        $_finalPrice =$_regularPrice ;
                        $_finalPrice =$_price;
                    else:
                        $_regularPrice 	= $_finalPrice;
                        $_price 		= $_finalPrice;
                    endif;
                endif;
            else:
                if($_finalPrice < $_price):
                    $_finalPrice = $_price;
                endif;
            endif;
            // echo $_finalPrice;
        endif;
        if($_finalPrice > 0){
            return $_finalPrice;
        }else{
            return $product->getData('price');
        }
        
        
    }
    public function getAmountForDisplay($product)
    {
        $store = $product->getStore();
        if($this->_weeeConfig->isEnabled($store)) {
            $this->_weeeTax->getWeeeAmount($product, null, null, null, $this->_weeeHelper->typeOfDisplay($product, 1));
        }
        return 0;
    }
    /**
     * Get base price with apply Group, Tier, Special prises
     *
     * @param Product $product
     * @param float|null $qty
     *
     * @return float
     */
    public function getBasePrice($product, $qty = null)
    {
        $price = (float) $product->getPrice();
        return min(
            $this->_applyTierPrice($product, $qty, $price),
            $this->_applySpecialPrice($product, $price)
        );
    }

    /**
     * Retrieve product final price
     *
     * @param float|null $qty
     * @param Product $product
     * @return float
     */
    public function getFinalPrice($qty, $product)
    {
        if ($qty === null && $product->getCalculatedFinalPrice() !== null) {
            return $product->getCalculatedFinalPrice();
        }

        $finalPrice = $this->getBasePrice($product, $qty);
        $product->setFinalPrice($finalPrice);

        $this->_eventManager->dispatch('catalog_product_get_final_price', ['product' => $product, 'qty' => $qty]);

        $finalPrice = $product->getData('final_price');
        $finalPrice = $this->_applyOptionsPrice($product, $qty, $finalPrice);
        $finalPrice = max(0, $finalPrice);
        $product->setFinalPrice($finalPrice);

        return $finalPrice;
    }

    /**
     * @param Product $product
     * @param float $productQty
     * @param Product $childProduct
     * @param float $childProductQty
     * @return float
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getChildFinalPrice($product, $productQty, $childProduct, $childProductQty)
    {
        return $this->getFinalPrice($childProductQty, $childProduct);
    }

    /**
     * Gets the 'tear_price' array from the product
     *
     * @param Product $product
     * @param string $key
     * @param bool $returnRawData
     * @return array
     */
    protected function getExistingPrices($product, $key, $returnRawData = false)
    {
        $prices = $product->getData($key);

        if ($prices === null) {
            $attribute = $product->getResource()->getAttribute($key);
            if ($attribute) {
                $attribute->getBackend()->afterLoad($product);
                $prices = $product->getData($key);
            }
        }

        if ($prices === null || !is_array($prices)) {
            return ($returnRawData ? $prices : []);
        }

        return $prices;
    }

    /**
     * Returns the website to use for group or tier prices, based on the price scope setting
     *
     * @return int|mixed
     */
    protected function getWebsiteForPriceScope()
    {
        $websiteId = 0;
        $value = $this->config->getValue('catalog/price/scope', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
        if ($value != 0) {
            // use the website associated with the current store
            $websiteId = $this->_storeManager->getWebsite()->getId();
        }
        return $websiteId;
    }

    /**
     * Apply tier price for product if not return price that was before
     *
     * @param   Product $product
     * @param   float $qty
     * @param   float $finalPrice
     * @return  float
     */
    protected function _applyTierPrice($product, $qty, $finalPrice)
    {
        if ($qty === null) {
            return $finalPrice;
        }

        $tierPrice = $product->getTierPrice($qty);
        if (is_numeric($tierPrice)) {
            $finalPrice = min($finalPrice, $tierPrice);
        }
        return $finalPrice;
    }

    /**
     * Get product tier price by qty
     *
     * @param   float $qty
     * @param   Product $product
     * @return  float|array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getTierPrice($qty, $product)
    {
        $allGroupsId = $this->getAllCustomerGroupsId();

        $prices = $this->getExistingPrices($product, 'tier_price', true);
        if ($prices === null || !is_array($prices)) {
            if ($qty !== null) {
                return $product->getPrice();
            } else {
                return [
                    [
                        'price' => $product->getPrice(),
                        'website_price' => $product->getPrice(),
                        'price_qty' => 1,
                        'cust_group' => $allGroupsId,
                    ]
                ];
            }
        }

        $custGroup = $this->_getCustomerGroupId($product);
        if ($qty) {
            $prevQty = 1;
            $prevPrice = $product->getPrice();
            $prevGroup = $allGroupsId;

            foreach ($prices as $price) {
                if ($price['cust_group'] != $custGroup && $price['cust_group'] != $allGroupsId) {
                    // tier not for current customer group nor is for all groups
                    continue;
                }
                if ($qty < $price['price_qty']) {
                    // tier is higher than product qty
                    continue;
                }
                if ($price['price_qty'] < $prevQty) {
                    // higher tier qty already found
                    continue;
                }
                if ($price['price_qty'] == $prevQty &&
                    $prevGroup != $allGroupsId &&
                    $price['cust_group'] == $allGroupsId) {
                    // found tier qty is same as current tier qty but current tier group is ALL_GROUPS
                    continue;
                }
                if ($price['website_price'] < $prevPrice) {
                    $prevPrice = $price['website_price'];
                    $prevQty = $price['price_qty'];
                    $prevGroup = $price['cust_group'];
                }
            }
            return $prevPrice;
        } else {
            $qtyCache = [];
            foreach ($prices as $priceKey => $price) {
                if ($price['cust_group'] != $custGroup && $price['cust_group'] != $allGroupsId) {
                    unset($prices[$priceKey]);
                } elseif (isset($qtyCache[$price['price_qty']])) {
                    $priceQty = $qtyCache[$price['price_qty']];
                    if ($prices[$priceQty]['website_price'] > $price['website_price']) {
                        unset($prices[$priceQty]);
                        $qtyCache[$price['price_qty']] = $priceKey;
                    } else {
                        unset($prices[$priceKey]);
                    }
                } else {
                    $qtyCache[$price['price_qty']] = $priceKey;
                }
            }
        }

        return $prices ? $prices : [];
    }

    /**
     * Gets the CUST_GROUP_ALL id
     *
     * @return int
     */
    protected function getAllCustomerGroupsId()
    {
        // ex: 32000
        return $this->_groupManagement->getAllCustomersGroup()->getId();
    }

    /**
     * Gets list of product tier prices
     *
     * @param Product $product
     * @return \Magento\Catalog\Api\Data\ProductTierPriceInterface[]
     */
    public function getTierPrices($product)
    {
        $prices = [];
        $tierPrices = $this->getExistingPrices($product, 'tier_price');
        foreach ($tierPrices as $price) {
            /** @var \Magento\Catalog\Api\Data\ProductTierPriceInterface $tierPrice */
            $tierPrice = $this->tierPriceFactory->create()
                ->setExtensionAttributes($this->tierPriceExtensionFactory->create());
            $tierPrice->setCustomerGroupId($price['cust_group']);
            if (array_key_exists('website_price', $price)) {
                $value = $price['website_price'];
            } else {
                $value = $price['price'];
            }
            $tierPrice->setValue($value);
            $tierPrice->setQty($price['price_qty']);
            if (isset($price['percentage_value'])) {
                $tierPrice->getExtensionAttributes()->setPercentageValue($price['percentage_value']);
            }
            $websiteId = isset($price['website_id']) ? $price['website_id'] : $this->getWebsiteForPriceScope();
            $tierPrice->getExtensionAttributes()->setWebsiteId($websiteId);
            $prices[] = $tierPrice;
        }
        return $prices;
    }

    /**
     * Sets list of product tier prices
     *
     * @param Product $product
     * @param \Magento\Catalog\Api\Data\ProductTierPriceInterface[] $tierPrices
     * @return $this
     */
    public function setTierPrices($product, array $tierPrices = null)
    {
        // null array means leave everything as is
        if ($tierPrices === null) {
            return $this;
        }

        $allGroupsId = $this->getAllCustomerGroupsId();
        $websiteId = $this->getWebsiteForPriceScope();

        // build the new array of tier prices
        $prices = [];
        foreach ($tierPrices as $price) {
            $extensionAttributes = $price->getExtensionAttributes();
            $priceWebsiteId = $websiteId;
            if (isset($extensionAttributes) && is_numeric($extensionAttributes->getWebsiteId())) {
                $priceWebsiteId = (string)$extensionAttributes->getWebsiteId();
            }
            $prices[] = [
                'website_id' => $priceWebsiteId,
                'cust_group' => $price->getCustomerGroupId(),
                'website_price' => $price->getValue(),
                'price' => $price->getValue(),
                'all_groups' => ($price->getCustomerGroupId() == $allGroupsId),
                'price_qty' => $price->getQty(),
                'percentage_value' => $extensionAttributes ? $extensionAttributes->getPercentageValue() : null
            ];
        }
        $product->setData('tier_price', $prices);

        return $this;
    }

    /**
     * @param Product $product
     * @return int
     */
    protected function _getCustomerGroupId($product)
    {
        if ($product->getCustomerGroupId() !== null) {
            return $product->getCustomerGroupId();
        }
        return $this->_customerSession->getCustomerGroupId();
    }

    /**
     * Apply special price for product if not return price that was before
     *
     * @param   Product $product
     * @param   float $finalPrice
     * @return  float
     */
    protected function _applySpecialPrice($product, $finalPrice)
    {
        return $this->calculateSpecialPrice(
            $finalPrice,
            $product->getSpecialPrice(),
            $product->getSpecialFromDate(),
            $product->getSpecialToDate(),
            $product->getStore()
        );
    }

    /**
     * Count how many tier prices we have for the product
     *
     * @param   Product $product
     * @return  int
     */
    public function getTierPriceCount($product)
    {
        $price = $product->getTierPrice();
        return count($price);
    }

    /**
     * Get formatted by currency tier price
     *
     * @param   float $qty
     * @param   Product $product
     * @return  array|float
     */
    public function getFormatedTierPrice($qty, $product)
    {
        $price = $product->getTierPrice($qty);
        if (is_array($price)) {
            foreach (array_keys($price) as $index) {
                $price[$index]['formated_price'] = $this->priceCurrency->convertAndFormat(
                    $price[$index]['website_price']
                );
            }
        } else {
            $price = $this->priceCurrency->format($price);
        }

        return $price;
    }

    /**
     * Get formatted by currency product price
     *
     * @param   Product $product
     * @return  array || float
     */
    public function getFormatedPrice($product)
    {
        return $this->priceCurrency->format($product->getFinalPrice());
    }

    /**
     * Apply options price
     *
     * @param Product $product
     * @param int $qty
     * @param float $finalPrice
     * @return float
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _applyOptionsPrice($product, $qty, $finalPrice)
    {
        $optionIds = $product->getCustomOption('option_ids');
        if ($optionIds) {
            $basePrice = $finalPrice;
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                if ($option = $product->getOptionById($optionId)) {
                    $confItemOption = $product->getCustomOption('option_' . $option->getId());

                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setConfigurationItemOption($confItemOption);
                    $finalPrice += $group->getOptionPrice($confItemOption->getValue(), $basePrice);
                }
            }
        }

        return $finalPrice;
    }

    /**
     * Calculate product price based on special price data and price rules
     *
     * @param   float $basePrice
     * @param   float $specialPrice
     * @param   string $specialPriceFrom
     * @param   string $specialPriceTo
     * @param   bool|float|null $rulePrice
     * @param   mixed|null $wId
     * @param   integer|null $gId
     * @param   int|null $productId
     * @return  float
     */
    public function calculatePrice(
        $basePrice,
        $specialPrice,
        $specialPriceFrom,
        $specialPriceTo,
        $rulePrice = false,
        $wId = null,
        $gId = null,
        $productId = null
    ) {
        \Magento\Framework\Profiler::start('__PRODUCT_CALCULATE_PRICE__');
        if ($wId instanceof Store) {
            $sId = $wId->getId();
            $wId = $wId->getWebsiteId();
        } else {
            $sId = $this->_storeManager->getWebsite($wId)->getDefaultGroup()->getDefaultStoreId();
        }

        $finalPrice = $basePrice;

        $finalPrice = $this->calculateSpecialPrice(
            $finalPrice,
            $specialPrice,
            $specialPriceFrom,
            $specialPriceTo,
            $sId
        );

        if ($rulePrice === false) {
            $date = $this->_localeDate->scopeDate($sId);
            $rulePrice = $this->_ruleFactory->create()->getRulePrice($date, $wId, $gId, $productId);
        }

        if ($rulePrice !== null && $rulePrice !== false) {
            $finalPrice = min($finalPrice, $rulePrice);
        }

        $finalPrice = max($finalPrice, 0);
        \Magento\Framework\Profiler::stop('__PRODUCT_CALCULATE_PRICE__');
        return $finalPrice;
    }

    /**
     * Calculate and apply special price
     *
     * @param float $finalPrice
     * @param float $specialPrice
     * @param string $specialPriceFrom
     * @param string $specialPriceTo
     * @param int|string|Store $store
     * @return float
     */
    public function calculateSpecialPrice(
        $finalPrice,
        $specialPrice,
        $specialPriceFrom,
        $specialPriceTo,
        $store = null
    ) {
        if ($specialPrice !== null && $specialPrice != false) {
            if ($this->_localeDate->isScopeDateInInterval($store, $specialPriceFrom, $specialPriceTo)) {
                $finalPrice = min($finalPrice, $specialPrice);
            }
        }
        return $finalPrice;
    }

    /**
     * Check is tier price value fixed or percent of original price
     *
     * @return bool
     */
    public function isTierPriceFixed()
    {
        return true;
    }
}
