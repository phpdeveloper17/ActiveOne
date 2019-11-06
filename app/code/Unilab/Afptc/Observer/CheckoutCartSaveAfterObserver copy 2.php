<?php

namespace Unilab\Afptc\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use \Magento\Store\Model\StoreManagerInterface;

class CheckoutCartSaveAfterObserver implements ObserverInterface
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
    protected $serializer;
    protected $checkoutSession;
    /**
     * CheckoutCartSaveAfter constructor.
     *
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product $product,
        \Magento\Checkout\Model\Cart $checkout,
        \Unilab\Afptc\Helper\Data $afptcHelper,
        \Unilab\Afptc\Logger\Logger $afptcLogger,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Unilab\Afptc\Model\ResourceModel\Afptc $afptcRule,
        \Unilab\Afptc\Model\Rule $afptcModel,
        \Magento\Checkout\Model\Session $checkoutSession
        )
    {
        $this->productRepository = $product;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_store = $storeManager;
        $this->afptcHelper = $afptcHelper;
        $this->coreRegistry = $coreRegistry;
        $this->product = $product;
        $this->checkout = $checkout;
        $this->afptcLogger = $afptcLogger;
        $this->serializer = $serializer;
        $this->afptcRule = $afptcRule;
        $this->afptcModel = $afptcModel;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        try{
            $debugArray = [];//initialize array
            if ($this->afptcHelper->extensionDisabled()) {
                return $this;
            }
            $cart = $observer->getCart();

            $store = $this->_store->getStore();
            $rulesCollection = $this->afptcRule->getActiveRulesCollection($store);
            
            foreach ($rulesCollection as $ruleModel) {
                $id = $ruleModel->getId();
                if (!$this->afptcModel->validate($cart)) {
                    continue;
                }
                if (isset($_stopFlag)) {
                    break;
                }
                if ($ruleModel->getStopRulesProcessing()) {
                    $_stopFlag = true;
                }
                // if ($ruleModel->getShowPopup()) {
                //     continue;
                // }
                if ($this->afptcHelper->getDeclineRuleCookie($ruleModel->getId())){
                    continue;
                }
                $step_discount='';
                foreach ($ruleModel->getData() as $_key => $value) {
                    if($_key == 'conditions_serialized'):
                        $step_discount = $value;
                    endif;
                }

                $getsku = $this->serializer->unserialize($step_discount);
                $sku = '';
                $sku_w = "";
                $sku_1st="";
                if(!empty($getsku)):
                    foreach($getsku as $key=>$_value):
                        if(!empty($_value[0]['conditions'])):
                            foreach($_value[0]['conditions'] as $_key => $value):
                                $sku    = $value['value'];
                            if($ruleModel->gettwo_promoitem() == true){
                                $debugArray[$id]['aftergettwo_promoitem'] = 'result ='.$ruleModel->gettwo_promoitem();
                                $debugArray[$id]['aftergettwo_promoitemSKuW!=sku'] = $sku_w .'!='. $sku;
                                if($sku_w != $sku){
                                    $sku_1st = $sku_w;
                                }
                            $sku_w   = $sku;
                            
                            }
                            endforeach;
                        endif;
                    endforeach;
                endif;
                $y_qty=0;
                $y_qty_1st=0;
                $QTYForCheck=0;
                $debugArray[$id]['skuImplode'] = $sku;
                foreach ($cart->getQuote()->getAllItems() as $_cartItem) {
                    //explode
                    foreach (explode(",", $sku) as $sku_value) {
                        if($sku_value ==  $_cartItem->getProduct()->getsku())
                        {
                            $y_qty  = $_cartItem->getQty();
                            $QTYForCheck  = $_cartItem->getQty();
                        }
                    }
                    if($ruleModel->gettwo_promoitem() == true){
                        foreach (explode(",", $sku_1st) as $sku_value) {
                            $debugArray[$id]['sku_value==cartItemgetSku'] = $sku_value .'=='.  $_cartItem->getProduct()->getsku();
                            if($sku_value ==  $_cartItem->getProduct()->getsku()){
                                $y_qty_1st  = $_cartItem->getQty();
                            }
                        }
                    }
                    
                }
                $finalY  =  $ruleModel->gety_qty();
                
                if($y_qty >= $ruleModel->getdiscount_step()){ //update for Target Qty
                    if($ruleModel->getauto_increment() == true){
                        $countYInc  = 0;
                        $QtyCart    = $y_qty;
                        
                        if($ruleModel->gettwo_promoitem() == true){
                            $QtyCart_1st    = $y_qty_1st;
                            $addfreeitemtocart = 0;
                            $debugArray[$id]['Inside_gettwo_promoitem'] = $QtyCart .'>='. $ruleModel->getdiscount_step() .'&&'. $QtyCart_1st .'>='. $ruleModel->getdiscount_step();
                            while($QtyCart >= $ruleModel->getdiscount_step() && $QtyCart_1st >= $ruleModel->getdiscount_step()){ 
                                $addfreeitemtocart = 1;   
                                $QtyCart = $QtyCart - $ruleModel->getdiscount_step();
                                $QtyCart_1st = $QtyCart_1st - $ruleModel->getdiscount_step();
                                $countYInc++;
                            }
                        }else{
                            $debugArray[$id]['else_QtyCart>=getdiscount_step'] = $QtyCart .'>='. $ruleModel->getdiscount_step();
                            while($QtyCart >= $ruleModel->getdiscount_step()){    
                                $QtyCart = $QtyCart - $ruleModel->getdiscount_step();
                                $debugArray[$id]['elseInsideWhileQtyCart'] = $QtyCart;
                                $countYInc++;               
                            }
                        }
                        $debugArray[$id]['countYInc >=1'] = $countYInc .'>=1';
                        if ($countYInc >=1):
                            $finalY = $ruleModel->gety_qty() * $countYInc;
                        endif;
                        $debugArray[$id]['AftercountYInc >=1'] = $finalY;
                    }
                    if($ruleModel->gettwo_promoitem() == true){
                        $debugArray[$id]['addfreeitemtocart'] = $addfreeitemtocart;
                        if($addfreeitemtocart == 1){
                            $productId  = $ruleModel->getproduct_id();   
                            $product_id =   $productId ;
                            $product = $this->product->load($product_id);
                            $cart = $this->checkout;
                            $cart->addProduct($product, array('qty' => $finalY));
                        }
                    }else{
                        $productId  = $ruleModel->getproduct_id();   
                        $product_id =   $productId;
                        $debugArray[$id]['else_ruleModel->gettwo_promoitem'] = 'result ='.$product_id;
                        // echo $product_id;
                        $product = $this->product->load($product_id);
                        $cart = $this->checkout;
                        
                        // product add to cart first then 
                        $cart->addProduct($product, array('qty' => 1));
                        //update qty when product promo is already in cart
                        $this->updateProductPromoQty($cart,$productId,$finalY);
                    }
                }else{
                    $productId  = $ruleModel->getproduct_id(); 
                   //remove promo product added in cart if product cart qty < Target Qty (Buy X)
                   foreach ($cart->getAllItems() as $items) {	
                        $product_ids = $items->getProduct()->getId();	
                        if($product_ids == $productId):
                            $items->delete();
                        endif;
                    }
                }
                
            }
        }catch (\Exception $e) {
            $this->createLogsForDebugAfptc($e->getMessage());
            $this->afptcLogger->error($e);
        }
        if($this->getScopeConfigAfptcEnable() == 1){
            $this->createLogsForDebugAfptc($debugArray);
        }
        $cart->getQuote()->unsTotalsCollectedFlag(false)->collectTotals()->save();
        return $this;
    }
    public function updateProductPromoQty($cart,$ruleProductId,$finalY){
        foreach ($cart->getAllItems() as $items) {
            $product_ids = $items->getProduct()->getId();	
            if($product_ids == $ruleProductId):
                $itemId = $items->getItemId();
                $params[$itemId]['qty'] = $finalY;
                $cart->updateItems($params);
            endif;
        }
        return $this;
    }
    public function getScopeConfigAfptcEnable(){
        $this->scopeConfig = $this->_objectManager->create('\Magento\Framework\App\Config\ScopeConfigInterface');
        $debugconfig = $this->scopeConfig->getValue('checkout/cart/afptc_debug_enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $debugconfig;
    }
    public function createLogsForDebugAfptc($logarray) {
		return file_put_contents('./aftpcdebugObserver.txt', print_r($logarray,1).PHP_EOL,FILE_APPEND);
    }
}