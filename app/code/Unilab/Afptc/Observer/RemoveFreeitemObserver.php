<?php

namespace Unilab\Afptc\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use \Magento\Store\Model\StoreManagerInterface;

class RemoveFreeitemObserver implements ObserverInterface
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
        $this->productRepository = $product;
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
    public function execute(Observer $observer)
    {
        $cartHelper = $this->_objectManager->create("Magento\Checkout\Helper\Cart");
        $items = $cartHelper->getCart()->getItems();
        foreach ($items as $item) {
            if ($item->getProduct()->getId() == $productId) {
                $itemId = $item->getItemId();
                $cartHelper->getCart()->removeItem($itemId)->save();
            }
        }
        return $this;
    }

}