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
        \Unilab\Afptc\Model\Rule $afptcModel
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
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        
        $debugArray = []; //initialize array
        if ($this->afptcHelper->extensionDisabled()) {
            return $this;
        }
        $cart = $observer->getCart();
       
        $store = $this->_store->getStore();
        $rulesCollection = $this->afptcRule->getActiveRulesCollection($store);
        
        $debugArray['ruleCollectionQry'] = $rulesCollection->getSelect()->__toString();
        foreach ($rulesCollection as $ruleModel) {
            
            $id = $ruleModel->getId();
            $debugArray['rule_id'] = $id;

            if (!$this->afptcModel->validate($cart)) {
                continue;
            }
            $debugArray['aftervalidate'] = 'true';
            if (isset($_stopFlag)) {
                break;
            }
            
            if ($ruleModel->getStopRulesProcessing()) {
                $_stopFlag = true;
            }
            $debugArray['afterStopRulesProcessing'] = 'result ='.$ruleModel->getStopRulesProcessing();
            // if ($ruleModel->getShowPopup()) {
            //     continue;
            // }
            if (
                ($this->afptcHelper->getDeclineRuleCookie($ruleModel->getId()))
               
            ) {
                continue;
            }
            $debugArray['afterDeclineRuleCookie'] = 'result ='.$this->afptcHelper->getDeclineRuleCookie($ruleModel->getId());

            try {
                $this->afptcModel->apply($cart, null);
                
                $debugArray['afterApplyCart'] = 'true';
                
    			$canAdd = true; //
    			foreach ($cart->getAllItems() as $item) {	
                    $product_id = $item->getProduct()->getId();	
                    
    				if($product_id == $ruleModel->getproduct_id()):
    					// $canAdd = false; //
    				endif;
    			}		
			//add product
            // if($canAdd == true):	
               
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
                            $debugArray['notEmptyconditions'] = 'true';
                           foreach($_value[0]['conditions'] as $_key => $value):
                             
                             $sku    = $value['value'];
                             
                            if($ruleModel->gettwo_promoitem() == true){
                                $debugArray['aftergettwo_promoitem'] = 'result ='.$ruleModel->gettwo_promoitem();
                                if($sku_w != $sku){
                                    $debugArray['sku_wNOTeQsku'] = 'result ='.$sku_w .'!='. $sku;
                                    $sku_1st = $sku_w;
                                    $debugArray['sku_1st'] = 'result ='.$sku_1st;
                                }
                            $sku_w   = $sku;
                            }
						   endforeach;
						endif;
				   endforeach;
                endif;

                $debugArray['afterSku'] = 'result ='.$sku;

                $y_qty_1st = 0;
                $y_qty = 0;
                foreach ($cart->getQuote()->getAllItems() as $_cartItem) {
                    //explode
                    foreach (explode(",", $sku) as $sku_value) {
                       if($sku_value ==  $_cartItem->getProduct()->getsku())
                        {
                            $y_qty  = $_cartItem->getQty();
                        }
                    }
                    $debugArray['afterSku'] = 'result ='.$sku;
                    //
                    $debugArray['beforeTwopromoitem'] = 'result ='.$ruleModel->gettwo_promoitem();
                    if($ruleModel->gettwo_promoitem() == true){
                        foreach (explode(",", $sku_1st) as $sku_value) {
                            $debugArray['afterSku'] = 'result ='.$sku_value.' == '.$_cartItem->getProduct()->getsku();

                           if($sku_value ==  $_cartItem->getProduct()->getsku())
                            {
                                $y_qty_1st  = $_cartItem->getQty();
                            }   
                        }
                    }
                    
                    //
                }
                
                $finalY  =  $ruleModel->gety_qty();
                $addfreeitemtocart=0;
                $debugArray['beforeYqtyGreaterDiscountStep'] = 'result ='.$y_qty.' >= '.$ruleModel->getdiscount_step();
                if($y_qty >= $ruleModel->getdiscount_step()){ //update for Target Qty
                    $debugArray['beforeAutoIncrement'] = 'result ='.$ruleModel->getauto_increment();
                    if($ruleModel->getauto_increment() == true):
                         $countYInc  = 0;
                         $QtyCart    = $y_qty;  
                         //
                         $debugArray['beforeTwopromoitem'] = 'result ='.$ruleModel->gettwo_promoitem();
                        
                         if($ruleModel->gettwo_promoitem() == true){
                            $QtyCart_1st    = $y_qty_1st;
                            $addfreeitemtocart = 0;
                            
                            $debugArray['QtyCartGreaterEQdiscountStep'] = 'result ='.$QtyCart.' >= '.$ruleModel->getdiscount_step();
                            while($QtyCart >= $ruleModel->getdiscount_step()){ 
                                $addfreeitemtocart = 1;   
                                $QtyCart = $QtyCart - $ruleModel->getdiscount_step();
                                $QtyCart_1st = $QtyCart_1st - $ruleModel->getdiscount_step();
                                $countYInc++;  
                            }   
                         }else{
                            $debugArray['ElseTwopromoitem'] = 'result ='.$QtyCart.' >= '.$ruleModel->getdiscount_step();
                            while($QtyCart >= $ruleModel->getdiscount_step()){    
                                $QtyCart = $QtyCart - $ruleModel->getdiscount_step();
                                $countYInc++;               
                            }
                         }
                         //
                         $debugArray['countYIncGreaterEQ1'] = 'result ='.$QtyCart.' >= '.$countYInc.' >= 1';
                       if ($countYInc >=1):
                            $finalY = $ruleModel->gety_qty() * $countYInc;
                        endif;  
                    endif;
                    $debugArray['beforeGettwo_promoitem'] = 'FinalY = '.$finalY;
                    // if($canAdd == true):    //
                        if($ruleModel->gettwo_promoitem() == true){
                            if($addfreeitemtocart == 1){
                                
                                $productId  = $ruleModel->getproduct_id();   
                                $product_id =   $productId ;
                                $product = $this->product->load($product_id);
                                $cart = $this->checkout;
                                $cart->addProduct($product, array('qty' => $finalY));
                                $debugArray['AfterAddproduct'] = 'result ='.$product_id;
                            }
                        }else{
                            
                            $debugArray['elseAfterAddproduct'] = 'result ='.$product_id;
                            $productId  = $ruleModel->getproduct_id();
                                   
                            $product_id =   $productId;
                            $product = $this->product->load($product_id);
                            
                            $cart = $this->checkout;
                            
                            //remove free products to add new free product to cart, issue in qty, apply only in disable[auto_increment,2promo_items]
                            if($ruleModel->getauto_increment() == false || $ruleModel->gettwo_promoitem() == false):
                                foreach ($cart->getAllItems() as $items) {	
                                    $product_ids = $items->getProduct()->getId();	
                                    if($product_ids == $product_id):
                                        $itemId = $items->getItemId();
                                        $cart->removeItem($itemId);
                                    endif;
                                }
                            endif;
                            
                            
                            $cart->addProduct($product, array('qty' => $finalY));
                            $cart->save();
                        }
    				// endif; //
                }
            } catch (\Exception $e) {
                $this->afptcLogger->error($e);
            }
        }
       
        if($this->getScopeConfigAfptcEnable() == 1){
            // $this->createLogsForDebugAfptc($debugArray);
        }
        $cart->getQuote()->unsTotalsCollectedFlag(false)->collectTotals()->save();
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