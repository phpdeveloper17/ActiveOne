<?php

namespace Unilab\Afptc\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use \Magento\Store\Model\StoreManagerInterface;

class CObserver implements ObserverInterface
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;
    protected $_store;
    protected $afptcHelper;
    protected $product;
    protected $checkout;
    protected $afptcLogger;

    /**
     * CheckoutCartSaveAfter constructor.
     *
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Checkout\Model\Cart $checkout,
        \Unilab\Afptc\Helper\Data $afptcHelper,
        \Unilab\Afptc\Logger\Logger $afptcLogger
        )
    {
        $this->productRepository = $productRepository;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_store = $storeManager;
        $this->afptcHelper = $afptcHelper;
        $this->coreRegistry = $coreRegistry;
        $this->product = $product;
        $this->checkout = $checkout;
        $this->afptcLogger = $afptcLogger;

    }

    /**
     * {@inheritdoc}
     */
    public function checkoutCartSaveAfter(Observer $observer)
    {
        if ($this->afptcHelper->extensionDisabled()) {
            return $this;
        }
        $cart = $observer->getCart();
        $store = $this->_store->getStore();
        $rulesCollection = $this->_objectManger->create("Unilab\Afptc\Model\Rule")->getActiveRulesCollection($store);
        foreach ($rulesCollection as $ruleModel) {
            $id = $ruleModel->getId();
            $ruleModel->load($ruleModel->getId());
            if (!$ruleModel->validate($cart)) {
                continue;
            }
            if (isset($_stopFlag)) {
                break;
            }
            if ($ruleModel->getStopRulesProcessing()) {
                $_stopFlag = true;
            }
            if ($ruleModel->getShowPopup()) {
                continue;
            }
            if (
                ($this->afptcHelper->getDeclineRuleCookie($ruleModel->getId())
                || $this->coreRegistry->register('rule_decline') == $ruleModel->getId())
                && !$this->coreRegistry->register('ignore_decline')
            ) {
                continue;
            }

            try {
                  // $ruleModel->apply($cart, null);
    			// $canAdd = true; //
    			foreach ($cart->getAllItems() as $item) {	
    				$product_id = $item->getProduct()->getId();	
    				if($product_id == $ruleModel->getproduct_id()):
    					// $canAdd = false; //
    				endif;
    			}		
			// //add product
			// if($canAdd == true):	
            foreach ($ruleModel as $_key => $value) {
                if($_key == '_origData'):
                    foreach ($value as $key => $_value) {
                        if($key == 'conditions_serialized'):
                                $step_discount = $_value;
                        endif;
                    }
                endif;
            }
                $getsku = unserialize($step_discount);
				$sku_w = "";
				if(!empty($getsku)):
				   foreach($getsku as $key=>$_value):
						if(!empty($_value)):
						   foreach($_value as $_key=>$value):
							 $sku    = $value['conditions'][0]['value'];
                            //
                            if($ruleModel->gettwo_promoitem() == true){
                                if($sku_w != $sku){
                                    $sku_1st = $sku_w;
                                }
                            $sku_w   = $sku;
                            }
						   endforeach;
						endif;
				   endforeach;
				endif;
                foreach ($cart->getQuote()->getAllItems() as $_cartItem) {
                    //explode
                    foreach (explode(",", $sku) as $sku_value) {
                       if($sku_value ==  $_cartItem->getProduct()->getsku())
                        {
                            $y_qty  = $_cartItem->getQty();
                        }   
                    }
                    //
                    if($ruleModel->gettwo_promoitem() == true){
                        foreach (explode(",", $sku_1st) as $sku_value) {
                           if($sku_value ==  $_cartItem->getProduct()->getsku())
                            {
                                $y_qty_1st  = $_cartItem->getQty();
                            }   
                        }
                    }
                    //
                }
                $finalY  =  $ruleModel->gety_qty();

                if($y_qty >= $ruleModel->getdiscount_step()){ //update for Target Qty
                    if($ruleModel->getauto_increment() == true):  
                         $countYInc  = 0;
                         $QtyCart    = $y_qty;  
                         //
                         if($ruleModel->gettwo_promoitem() == true){
                            $QtyCart_1st    = $y_qty_1st;
                            $addfreeitemtocart = 0;
                            while($QtyCart >= $ruleModel->getdiscount_step() && $QtyCart_1st >= $ruleModel->getdiscount_step()){ 
                                $addfreeitemtocart = 1;   
                                $QtyCart = $QtyCart - $ruleModel->getdiscount_step();
                                $QtyCart_1st = $QtyCart_1st - $ruleModel->getdiscount_step();
                                $countYInc++;  
                            }   
                         }else{
                            while($QtyCart >= $ruleModel->getdiscount_step()){    
                                $QtyCart = $QtyCart - $ruleModel->getdiscount_step();
                                $countYInc++;               
                            }
                         }
                         //
                       if ($countYInc >=1):
                            $finalY = $ruleModel->gety_qty() * $countYInc;
                        endif;  
                    endif; 
                    // if($canAdd == true):    //
                        if($ruleModel->gettwo_promoitem() == true){
                            if($addfreeitemtocart == 1){
                                $productId  = $ruleModel->getproduct_id();          
                                $product_id =   $productId ;
                                $product = $this->product->load($product_id);
                                $cart = $this->checkout;
                                $cart->init();  
                                $cart->addProduct($product, array('qty' => $finalY));
                            }
                        }else{
                            $productId  = $ruleModel->getproduct_id();          
                            $product_id =   $productId ;
                            $product = $this->product->load($product_id);
                            $cart = $this->checkout;
                            $cart->init();
                            $cart->addProduct($product, array('qty' => $finalY));
                        }
    				// endif; //
                }
            } catch (\Exception $e) {
                $this->afptcLogger->error($e);
            }
        }

        $cart->getQuote()->unsTotalsCollectedFlag()->collectTotals()->save();
        return $this;
    }

    public function quoteAfterLoad($observer)
    {

        $quote = $observer->getEvent()->getQuote();
        foreach ($quote->getAllItems() as $_item) {
            $ruleIdOption = $_item->getOptionByCode('aw_afptc_rule');
            if (null === $ruleIdOption || !$ruleIdOption->getValue()) {
                continue;
            }
            $ruleModel = $this->_objectManager->create("Unilab\Afptc\Model\Rule")->load($ruleIdOption->getValue());

            $storeId = $this->_store->getStore()->getId();
            $customerGroup =$this->afptcHelper->getCustomerGroup();
            if (
                null === $ruleModel->getId()
                || (0 != $ruleModel->getStoreIds() && !in_array($storeId, explode(',', $ruleModel->getStoreIds())))
                || !in_array($customerGroup, explode(',', $ruleModel->getCustomerGroups()))
            ) {
                $quote->removeItem($_item->getId());
                $_needRecollectFlag = true;
            }
        }
        if (isset($_needRecollectFlag)) {
            $quote->unsTotalsCollectedFlag()->collectTotals()->save();
        }
        return $this;
    }

    public function checkoutCartProductAddAfter()
    {

        if ($this->afptcHelper->extensionDisabled() || !$this->afptcHelper->isAllowReAddToCart()) {
            return $this;
        }

        $this->afptcHelper->removeDeclineCookies();
        $this->coreRegistry->register('ignore_decline', 1, true);
        return $this;
    }

    public function salesQuoteRemoveAfter($observer)
    {

        if ($this->aftpcHelper->extensionDisabled() || $this->afptcHelper->isAllowReAddToCart()) {
            return $this;
        }

        $ruleIdOption = $observer->getQuoteItem()->getOptionByCode('aw_afptc_rule');
        if (null !== $ruleIdOption) {
            $this->aftpcHelper->setDeclineRuleCookie($ruleIdOption->getValue());
            $this->coreRegistry->register('rule_decline', $ruleIdOption->getValue(), true);
        }

    }

    public function getFinalPrice($observer)
    {

        if ($this->afptcHelper->extensionDisabled()) {
            return $this;
        }

        $ruleDiscount = $observer->getProduct()->getCustomOption('aw_afptc_discount');
        if (null !== $ruleDiscount) {
            $finalPrice = $observer->getProduct()->getFinalPrice();
            $observer->getProduct()->setFinalPrice(max(0, $finalPrice - ($finalPrice * $ruleDiscount->getValue() / 100)));
            $observer->getProduct()->addCustomOption('option_ids', null);

            $version = $this->_objectManager->create("\Magento\Framework\App\ProductMetadataInterface");
            if (version_compare($version->getVersion(), '1.4.1.1', '<=')
                && $observer->getProduct()->getTypeInstance(true) instanceof \Magento\ConfigurableProduct\Model\Product\Type\Configurable
            ) {
                $attributes = $observer
                    ->getProduct()
                    ->getTypeInstance(true)
                    ->getConfigurableAttributes($observer->getProduct())
                ;
                /* @var $attribute Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute */
                foreach ($attributes as $attribute) {
                    $priceData = $attribute->getPrices();
                    foreach ($priceData as $key => $price) {
                        if (!$price['is_percent']) {
                            $price['pricing_value'] = max(
                                0,
                                $price['pricing_value'] - ($price['pricing_value'] * $ruleDiscount->getValue() / 100)
                            );
                        }
                        $priceData[$key] = $price;
                    }
                    $attribute->setPrices($priceData);
                }
                $observer->getProduct()->setData('_cache_instance_configurable_attributes', $attributes);
            }
        }
        return $this;
    }

    public function getConfigurablePrice(Observer $observer)
    {

        if ($this->afptcHelper->extensionDisabled()) {
            return $this;
        }

        $product = $observer->getEvent()->getProduct();
        $ruleDiscount = $product->getCustomOption('aw_afptc_discount');
        if (null === $ruleDiscount) {
            return $this;
        }

        $configurablePrice = $observer->getProduct()->getConfigurablePrice();
        $configurablePrice = max(0, $configurablePrice - ($configurablePrice * $ruleDiscount->getValue() / 100));
        $product->setConfigurablePrice($configurablePrice);

        return $this;
    }


    public function checkoutCartUpdateItemsAfter(Observer $observer)
    {
        $cart = $observer->getCart();
        $store = $this->_store;

        $rulesCollection = $this->_objectManager->create("Unilab\Afptc\Model\Rule")->getActiveRulesCollection($store);

        foreach ($rulesCollection as $ruleModel) 
        {
            $id = $ruleModel->getId();
            foreach ($ruleModel as $_key => $value) {
                if($_key == '_origData'):
                    foreach ($value as $key => $_value) {
                        if($key == 'conditions_serialized'):
                                $step_discount = $_value;
                                $getsku = unserialize($step_discount);
                               foreach($getsku as $key=>$_value):
                                   foreach($_value as $_key=>$value):
                                            $sku = $value['conditions'][0]['value'];
                                                foreach ($cart->getQuote()->getAllVisibleItems() as $item) {
                                                    if( $sku == $item->getSku()):
                                                        $this->afptctLogger->error(sprint_f($id, null, "checkoutCartUpdateItemsAfter"));
                                                        if($ruleModel->gettwo_promoitem() == 1){
                                                            // Mage::log($sku, null, "promolog.log");
                                                        }else{
                                                            // $productId = $ruleModel->getProductId();
                                                            // $this->removeFreeitem($productId);
                                                        }
                                                    endif;
                                                }                                   
                                   endforeach;
                               endforeach;
                        endif;
                    }
                endif;
            }
        }
    }

    function removeFreeitem($productId)
    {

       $cartHelper = $this->_objectManager->create("Magento\Checkout\Helper\Cart");
        $items = $cartHelper->getCart()->getItems();
        foreach ($items as $item) {
            if ($item->getProduct()->getId() == $productId) {
                $itemId = $item->getItemId();
                $cartHelper->getCart()->removeItem($itemId)->save();
            }
        }    

    }
}