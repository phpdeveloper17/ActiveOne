<?php

namespace Unilab\DigitalCouponing\Model;

class DigitalCoupon extends \Magento\Framework\Model\AbstractModel
{
    const SCOPE_STORE = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Unilab\DigitalCouponing\Model\AsciiFactory $asciiFactory,
        \Unilab\DigitalCouponing\Model\RemainderFactory $remainderFactory,
        \Unilab\DigitalCouponing\Model\UsedcouponFactory $usedCouponFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Data\Form\FormKey $formKey
    )
    {
        $this->resource = $resource;
        $this->cart = $cart;
        $this->scopeConfig = $scopeConfig;
        $this->asciiFactory = $asciiFactory;
        $this->remainderFactory = $remainderFactory;
        $this->usedCouponFactory = $usedCouponFactory;
        $this->customerSession = $customerSession;
        $this->productRepository = $productRepository;
        $this->formKey = $formKey;
    }

    
    public function validateInput($unilabDc)
    {
        // $inputData      = $postData;
        $dcInput        = $unilabDc; //$inputData['dcinput'];
        // $productId      = $inputData['productid'];
        // $qty            = $inputData['qty'];
        // $quoteId        = $inputData['quoteid'];
        $key            = $this->getConfigValue('unilabdc/unilabdc_general/unilabdc_key');
        $dcRemainder    = substr($dcInput, 6, 1);
        $dcInputChopped = substr($dcInput, 0, -1);
        $dcInputSplit   = str_split($dcInputChopped);

        $sum = 0;

        foreach ($dcInputSplit as $value) :
            $letter = $value;
            $asciiEquivalent = $this->getAscii($letter)->getAsciiEquivalent();
            $sum += $asciiEquivalent;
            $equivs[] = $asciiEquivalent;
        endforeach;

        $quotient   = floor(($sum / $key));
        $remainder  = $sum - ($quotient * $key);

       //return ['quotient' => $quotient, 'sum' => $sum, 'remainder' => $remainder, 'key' => $key];

        $remainderEquivalent = $this->getRemainder($remainder)->getLetter();
        

        if($dcRemainder == $remainderEquivalent) :
            $this->customerSession->setDcInput($dcInput);
            return true;
        else:
            return false;
        endif;
    }

    public function checkExistingCoupon($code, $quoteId)
    {
        $existing = false;

        if($this->getUsedCoupon($code)->getId()):
            $existing = true;
        endif;
 

        if($this->getQuoteItem($quoteId, $code, null)):
            $existing = true;
        endif;

        return $existing;
    }
    public function checkExistingProduct($quoteId, $productId)
    {
        $product = $this->getQuoteItem($quoteId, null, $productId);

        if($product) :
            return true;
        else :
            return false;
        endif;

    }
    public function addFreeToCart($productId, $qty, $quoteId)
    {
        if(isset($productId)) :
            $product = $this->productRepository->getById($productId);

            $productSku         = $product->getSku();
            $productSkuChopped  = substr($productSku, 0, -1);
            $freeProductSku     = $productSkuChopped . "PS";
            $freeProduct        = $this->productRepository->get($freeProductSku);

            $params = [
                'form_key' => $this->formKey->getFormKey(),
                'product' => $freeProduct->getId(),
                'qty' => $qty
            ];

            $this->cart->addProduct($product, $params);
            $this->cart->save();

        endif;
    }

    protected function getRemainder($remainder)
    {
        return $this->remainderFactory
                    ->create()
                    ->getCollection()
                    ->addFieldToFilter('remainder_equivalent', $remainder)
                    ->getFirstItem();
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
    protected function getAscii($letter)
    {
        return $this->asciiFactory
                    ->create()
                    ->getCollection()
                    ->addFieldToFilter('letter', $letter)
                    ->getFirstItem();
    }

    protected function getUsedCoupon($couponCode)
    {
        return $this->usedCouponFactory
                    ->create()
                    ->getCollection()
                    ->addFieldToFilter('couponcode', $couponCode)
                    ->getFirstItem();
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