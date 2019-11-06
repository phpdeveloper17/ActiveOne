<?php
/**
 * Afptc View XML.
 * @category  Unilab
 * @package   Unilab_Afptc
 * @author    Kristian Claridad
 */
namespace Unilab\Afptc\Model;

use Magento\Quote\Model\Quote\Address;
use Magento\Rule\Model\AbstractModel;

/**
 * Class Rule
 * @package Vendor\Rules\Model
 *
 * @method int|null getRuleId()
 * @method Rule setRuleId(int $id)
 */
class Rule extends AbstractModel
{
    const BY_PERCENT_ACTION  = 1;
    const BUY_X_GET_Y_ACTION = 2;
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'awafptc_rule';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getRule() in this case
     *
     * @var string
     */
    protected $_eventObject = 'rule';

    /** @var \Magento\SalesRule\Model\Rule\Condition\CombineFactory */
    protected $condCombineFactory;

    /** @var \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory */
    protected $condProdCombineF;

    /**
     * Store already validated addresses and validation results
     *
     * @var array
     */
    protected $validatedAddresses = [];

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\SalesRule\Model\Rule\Condition\CombineFactory $condCombineFactory
     * @param \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $condProdCombineF
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\SalesRule\Model\Rule\Condition\CombineFactory $condCombineFactory,
        \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $condProdCombineF,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->condCombineFactory = $condCombineFactory;
        $this->condProdCombineF = $condProdCombineF;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
    }

    /**
     * Set resource model and Id field name
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Unilab\Afptc\Model\ResourceModel\Afptc');
        $this->setIdFieldName('rule_id');
    }

    /**
     * Get rule condition combine model instance
     *
     * @return \Magento\SalesRule\Model\Rule\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->condCombineFactory->create();
    }
    public function loadPost(array $rule)
    {
        $arr = $this->_convertFlatToRecursive($rule);
        if (isset($arr['conditions'])) {
            $this->getConditions()->setConditions(array())->loadArray($arr['conditions'][1]);
        }
        return $this;
    }
    public function validate(\Magento\Framework\DataObject $object)
    {
        
        $_result = false;
        if ($object instanceof \Magento\Checkout\Model\Cart) {
            $this->_prepareValidate($object);
            
            //check if free product by rule already in cart
            $this->setBuyXProductIds(array());
            foreach ($object->getQuote()->getAllItems() as $item) {
                $ruleIdOption = $item->getOptionByCode('aw_afptc_rule');
                //Buy X get Y free
                if ($this->getSimpleAction() == self::BUY_X_GET_Y_ACTION
                    // issue with BuyX GetY free
                    && $item->getQty() >= $this->getDiscountStep()
                    && null === $ruleIdOption
                ) {
                    $_products = $this->getBuyXProductIds();
                    $_products[$item->getId()] = $item->getProductId();
                    $this->setBuyXProductIds($_products);
                }
                //check already applied rules and remove item if shopping cart not valid for rule
                if (null !== $ruleIdOption && $ruleIdOption->getValue() == $this->getId()) {
                    if ($this->getSimpleAction() == self::BUY_X_GET_Y_ACTION) {
                        $relatedItemIdOption = $item->getOptionByCode('aw_afptc_related_item_id');
                        $_relatedItemId = null;
                        if (null !== $relatedItemIdOption) {
                            $_relatedItemId = $relatedItemIdOption->getValue();
                            $_products = $this->getBuyXProductIds();
                            unset($_products[$_relatedItemId]);
                            $this->setBuyXProductIds($_products);
                        }
                        $itemModel = null;
                        foreach ($object->getQuote()->getItemsCollection() as $_item) {
                            if ($_item->getId() == $_relatedItemId) {
                                $itemModel = $_item;
                                break;
                            }
                        }

                        if (null === $itemModel
                            || null === $itemModel->getId()
                            || $itemModel->getQty() < $this->getDiscountStep()
                            || $itemModel->isDeleted()
                        ) {
                            $needRemoveFlag = true;
                        }
                    }

                    if (!$this->getConditions()->validate($object) || isset($needRemoveFlag)) {
                        //$object->removeItem($item->getId());
                    } else {
                        $this->setAlreadyApplied(true);
                    }

                    if ($this->getSimpleAction() == self::BUY_X_GET_Y_ACTION && $this->getBuyXProductIds() != 0) {
                        //last issue
                        $this->setAlreadyApplied(false);
                    }
                }
            }
            
            // $_result = $this->getConditions()->validate($object);
        }
        $_result = true;
        return $_result;

    }
    protected function _prepareValidate(\Magento\Checkout\Model\Cart $cart)
    {
        
        $cart->setData('all_items', $cart->getQuote()->getAllItems());
        if ($cart->getQuote()->isVirtual()) {
            $address = $cart->getQuote()->getBillingAddress();
        } else {
            $address = $cart->getQuote()->getShippingAddress();
        }
        
        foreach ($cart->getQuote()->getAllItems() as $item) {
            
            $ruleIdOption = $item->getOptionByCode('aw_afptc_rule');
           
            if (null === $ruleIdOption) {
                continue;
            }
            
            $address->setTotalQty($cart->getItemsQty() - $item->getQty());
           
            $address->setBaseSubtotal($address->getBaseSubtotal() - $item->getBaseRowTotal());
            $address->setWeight($address->getWeight() - $item->getWeight());
        }
        $version = $this->_objectManager->get('Magento\Framework\App\ProductMetadataInterface');
        
        // if ((\Unilab\Afptc\Helper\Data::getPlatform() == \Unilab\Afptc\Helper\Data::CE_PLATFORM
        //         && version_compare($version->getVersion(), '1.7', '>='))
        //     || (\Unilab\Afptc\Helper\Data::getPlatform() == \Unilab\Afptc\Helper\Data::EE_PLATFORM
        //         && version_compare($version->getVersion(), '1.14', '>='))
        // ) {
           
        // }
        $quote = $cart->getQuote();
        foreach ($quote->getAllItems() as $item) {
            
            $itemProduct = $item->getProduct();
           
            $product = $this->_objectManager->create("\Magento\Catalog\Model\Product")->load($itemProduct->getId());
            foreach ($product->getData() as $key => $value) {
                if (null === $itemProduct->getData($key)) {
                    $itemProduct->setData($key, $value);
                }
            }
        }
        return $this;
    }
    public function getDiscount()
    {
        $_discount = $this->getData('discount');
        if ($this->getSimpleAction() == self::BUY_X_GET_Y_ACTION) {
            $_discount = 100;
        }
        return $_discount;
    }
    public function apply(\Magento\Checkout\Model\Cart $cart, $relatedItemId = null)

    {

        if (!$this->validate($cart) || true === $this->getAlreadyApplied()) {
            return $this;
        }

        //***** Get Cart Current Quantity ***//
            $cartItem = 1;      
            foreach ($cart->getQuote()->getAllItems() as $_cartItem) {
                $cartItem = $_cartItem->getQty();       
            }

        //***** Get Cart Current Quantity - End***//

        // $itemModel = Mage::getModel('sales/quote_item');
        $itemModel = $this->_objectManager->create("\Magento\Sales\Model\Order");
        if ($this->getSimpleAction() == self::BUY_X_GET_Y_ACTION && count($this->getBuyXProductIds()) != 0) {

            if (null === $relatedItemId) {
                foreach ($this->getBuyXProductIds() as $itemId => $productId) {
                    $this->apply($cart, $itemId);
                }
                return $this;
            }

            if (array_key_exists($relatedItemId, $this->getBuyXProductIds())) {
                $itemModel = null;
                foreach ($cart->getQuote()->getItemsCollection() as $_item) {
                    if ($_item->getId() == $relatedItemId) {
                        $itemModel = $_item;
                        break;
                    }
                }
                if (null === $itemModel || null === $itemModel->getId()) {
                    return $this;
                }
                $itemModel = clone $itemModel;
                $_product = $itemModel->getProduct();
                $_product->addCustomOption('aw_afptc_related_item_id', $relatedItemId);
            }
        }

    if ($cartItem >= $this->getdiscount_step() && $this->getSimpleAction() == \Unilab\Afptc\Model\Rule::BY_PERCENT_ACTION && true !== $this->getAlreadyApplied()) {
        $_product = $this->_objectManager->create("Magento\Catalog\Model\Product")->load($this->getProductId());
    }

        if (!isset($_product)) {
            return $this;
        }

        if($_product->getTypeId() == 'downloadable') {
            if (!$_product->getTypeInstance(true)->getProduct($_product)->getLinksPurchasedSeparately()) {
                $links = $_product->getTypeInstance(true)->getLinks($_product);
                foreach($links as $link) {
                    $preparedLinks[] = $link->getId();
                }
                $_product->addCustomOption('downloadable_link_ids', implode(',', $preparedLinks));
            }
        }
        if (!isset($_product) || null === $_product->getId() || !$_product->isSaleable()) {
            return $this;
        }

        $_product->addCustomOption('aw_afptc_discount', min(100, $this->getDiscount()));
        $_product->addCustomOption('aw_afptc_rule', $this->getId());
        //***** check if auto increment is enabled ***//

            //$y_qty            = $this->getYQty($this->getId());

            $step_discount  = $this->getStepDiscount($this->getId());

            $getsku = unserialize($step_discount);

            foreach($getsku as $key=>$_value):
                foreach($_value as $_key=>$value):
                    $sku = $value['conditions'][0]['value'];
                endforeach;
            endforeach;

            foreach ($cart->getQuote()->getAllItems() as $_cartItem) {
                foreach (explode(",", $sku) as $sku_value) {
                   if($sku_value ==  $_cartItem->getProduct()->getsku())
                    {
                        $y_qty  = $_cartItem->getQty();
                    }
                }
            }
            $finalY  =  $this->getYQty($this->getId());
            if($this->getauto_increment() == true):
                $countYInc  = 0;
                $QtyCart    = $y_qty;
                while($QtyCart >= $this->getdiscount_step()){
                    $QtyCart = $QtyCart - $this->getdiscount_step();
                    $countYInc++;
                }
                if ($countYInc >=1):
                    $finalY = $this->gety_qty() * $countYInc;
                endif;                
            endif;
        //***** check if auto increment is enabled - End ***//  
        $itemModel
            ->setQuote($cart->getQuote())
            ->setStoreId($this->_objectManager->create("\Magento\Store\Model\StoreManagerInterface")->getStore()->getId())
            ->setOptions($_product->getCustomOptions())
            ->setProduct($_product)
            ->setQty($finalY);
         $cart->getQuote()->addItem($itemModel);
         $cart->save();
        return $this;
    }
     //*** get data directly from table ***//
    //get assigned free quantity (Y)
    protected function getYQty($rule_id)
    {
        $this->_resource = $this->_objectManager->get("\Magento\Framework\App\ResourceConnection");
        $connection = $this->_resource->getConnection('core_write');
        $connection->beginTransaction();
        //Search entity_id from customer_entity_varchar
        $grpQuery            =   $connection->select()->from('aw_afptc_rules', array('*'))->where('rule_id=?',$rule_id); 
        $rowArray            =   $connection->fetchRow($grpQuery);
        return $rowArray ['y_qty'];  
    }
    //get assigned product ID *not X*

    protected function getStepDiscount($rule_id)
    {
        $this->_resource = $this->_objectManager->get("\Magento\Framework\App\ResourceConnection");
        $connection = $this->_resource->getConnection('core_write');
        //Search entity_id from customer_entity_varchar
        $grpQuery            =   $connection->select()->from('aw_afptc_rules', array('*'))->where('rule_id=?',$rule_id); 
        $rowArray            =   $connection->fetchRow($grpQuery);
        return $rowArray ['conditions_serialized'];  

    }
    /**
     * Get rule condition product combine model instance
     *
     * @return \Magento\SalesRule\Model\Rule\Condition\Product\Combine
     */
    public function getActionsInstance()
    {
        return $this->condProdCombineF->create();
    }

    /**
     * Check cached validation result for specific address
     *
     * @param Address $address
     * @return bool
     */
    public function hasIsValidForAddress($address)
    {
        $addressId = $this->_getAddressId($address);
        return isset($this->validatedAddresses[$addressId]) ? true : false;
    }

    /**
     * Set validation result for specific address to results cache
     *
     * @param Address $address
     * @param bool $validationResult
     * @return $this
     */
    public function setIsValidForAddress($address, $validationResult)
    {
        $addressId = $this->_getAddressId($address);
        $this->validatedAddresses[$addressId] = $validationResult;
        return $this;
    }

    /**
     * Get cached validation result for specific address
     *
     * @param Address $address
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsValidForAddress($address)
    {
        $addressId = $this->_getAddressId($address);
        return isset($this->validatedAddresses[$addressId]) ? $this->validatedAddresses[$addressId] : false;
    }

    /**
     * Return id for address
     *
     * @param Address $address
     * @return string
     */
    private function _getAddressId($address)
    {
        if ($address instanceof Address) {
            return $address->getId();
        }
        return $address;
    }
}