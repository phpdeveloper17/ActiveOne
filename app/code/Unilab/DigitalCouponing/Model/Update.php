<?php

namespace Unilab\DigitalCouponing\Model;

class Update extends \Magento\Framework\Model\AbstractModel
{
    const SCOPE_STORE = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Unilab\DigitalCouponing\Model\UsedcouponFactory $usedCouponFactory,
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    )
    {
        $this->resource = $resource;
        $this->cart = $cart;
        $this->scopeConfig = $scopeConfig;
        $this->usedCouponFactory = $usedCouponFactory;
        $this->order = $order;
        $this->productRepository = $productRepository;
        $this->timezone = $timezone;
    }

    public function updateQuote($productId, $couponCode)
    {
        $cartItems = $this->cart->getQuote()->getAllItems();

        foreach($cartItems as $item) :
            if($item->getProductId() == $productId) :
                $item->setDcCoupon($couponCode);
                $item->setDcApplied(1);
                $item->save();
            endif;
        endforeach;
    }
    public function saveUsedCoupon($order)
    {
        $dateTime       = $this->timezone->scopeTimeStamp();
        $connection     = $this->getConnection();
        // $orderIncId     = $order->getIncrementId();
        
        // $order          = $this->order->loadByIncrementId($orderIncId);
        $customerEmail  = $order->getCustomerEmail();
        $entityId       = $order->getQuoteId();

        $quoteItemQuery = "SELECT `dc_coupon`, `product_id` FROM `quote_item` WHERE `quote_id` = '$entityId' AND `dc_coupon` IS NOT NULL";
        $queryResult    = $connection->fetchAll($quoteItemQuery);
        // $logger->info(count($queryResult));
        // $logger->info($queryResult[0]);
        if(count($queryResult)):
            foreach($order->getAllVisibleItems() as $item) :

                $productId  = $item->getProductId();
                $product    = $this->productRepository->getById($productId);
                $productSku = $product->getSku();
                //$productDc  = $product->getUnilabDc();
                foreach($queryResult as $item) :

                    if($item['product_id'] == $productId) :

                        try{
                            $usedCoupon = $this->usedCouponFactory->create();
                            $usedCoupon->setCouponCode($item['dc_coupon']);
                            $usedCoupon->setSku($productSku);
                            $usedCoupon->setCustomerEmail($customerEmail);
                            $usedCoupon->setCreatedDateTime($dateTime);
                            $usedCoupon->setOrderId($order->getId());
                            $usedCoupon->save();
                            $usedCoupon->unsetData();
                        } catch(\Exception $e) {
                            // $logger->info($e->getMessage());
                        }
                    endif;
    
                endforeach;
            endforeach;
        endif;

    }
    
    protected function getQuoteItem($quoteId, $couponCode = null, $productId = null)
    {

        if($couponCode && !$productId) :            
            $quoteItemQuery = "SELECT item_id FROM `quote_item` 
                           WHERE quote_id = '$quoteId' AND dc_coupon = '$couponCode'";
        else :
            $quoteItemQuery = "SELECT item_id FROM `quote_item` 
                           WHERE quote_id = '$quoteId' AND product_id = '$productId'";
        endif;
        
        
        $queryResult = $this->getConnection()->fetchRow($quoteItemQuery);

        if($queryResult['item_id']) :
            return true;
        else :
            return false;
        endif;
    }
    
    protected function getConfigValue($path)
    {
        return $this->scopeConfig->getValue($path, self::SCOPE_STORE);
    }

    protected function getConnection()
    {
        return $this->resource->getConnection();
    }
}