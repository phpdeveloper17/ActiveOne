<?php

namespace Unilab\Afptc\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use \Magento\Store\Model\StoreManagerInterface;

class QuoteAfterLoadObserver implements ObserverInterface
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

}