<?php

namespace Unilab\Afptc\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use \Magento\Store\Model\StoreManagerInterface;

class CheckoutCartUpdateItemsAfterObserver implements ObserverInterface
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
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Checkout\Model\Cart $checkout,
        \Unilab\Afptc\Helper\Data $afptcHelper,
        \Unilab\Afptc\Logger\Logger $afptcLogger,
        \Magento\Framework\Serialize\Serializer\Json $serializer
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

    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        $cart = $observer->getCart();
        $store = $this->_store->getStore();
       
        // $rulesCollection = $this->_objectManager->create("Unilab\Afptc\Model\ResourceModel\Afptc")->getActiveRulesCollection($store);

        // foreach ($rulesCollection as $ruleModel) 
        // {
        //     $id = $ruleModel->getId();
        //     foreach ($ruleModel->getData() as $_key => $value) {
        //         if($_key == 'conditions_serialized'):
        //             $step_discount = $value;
        //             $getsku = $this->serializer->unserialize($step_discount);
        //             foreach($getsku['conditions'] as $key=>$_value):
        //                 foreach($_value['conditions'] as $_key => $value):
        //                     $sku    = $value['value'];
        //                     foreach ($cart->getQuote()->getAllVisibleItems() as $item) {
        //                         if( $sku == $item->getSku()):
        //                             $this->afptcLogger->error(sprintf($id, null, "checkoutCartUpdateItemsAfter"));
        //                             //remove free product and checkoutcartsaveafterobserver execute free product
        //                             //reset quote total and price
        //                             $productId = $ruleModel->getProductId();
        //                             $cartHelper = $this->_objectManager->create("Magento\Checkout\Helper\Cart");
        //                             $items = $cartHelper->getCart()->getItems();
        //                             foreach ($items as $item) {
        //                                 if ($item->getProduct()->getId() == $productId) {
        //                                     $itemId = $item->getItemId();
        //                                     $cartHelper->getCart()->removeItem($itemId)->save();
        //                                 }
        //                             }
        //                         endif;
        //                     }
        //                 endforeach;
		// 		   endforeach;
        //         endif;
        //     }
            
        // }
        return $this;
    }

}