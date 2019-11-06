<?php

namespace Unilab\Afptc\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use \Magento\Store\Model\StoreManagerInterface;

class GetFinalPriceObserver implements ObserverInterface
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

        if ($this->afptcHelper->extensionDisabled()) {
            return $this;
        }

        $ruleDiscount = $observer->getProduct()->getCustomOption('aw_afptc_discount');
        if (null !== $ruleDiscount) {
            $finalPrice = $observer->getProduct()->getFinalPrice();
            $observer->getProduct()->setFinalPrice(max(0, $finalPrice - ($finalPrice * $ruleDiscount->getValue() / 100)));
            $observer->getProduct()->addCustomOption('option_ids', null);

            $version = $this->_objectManager->create("\Magento\Framework\App\ProductMetadataInterface");
            // if (version_compare($version->getVersion(), '1.4.1.1', '<=')
            //     && $observer->getProduct()->getTypeInstance(true) instanceof \Magento\ConfigurableProduct\Model\Product\Type\Configurable
            // ) {
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
            // }
        }
        return $this;
    }
}