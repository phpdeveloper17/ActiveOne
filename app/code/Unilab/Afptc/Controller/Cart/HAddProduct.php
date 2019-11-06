<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Afptc\Controller;

class AddProduct extends \Magento\Checkout\Controller\Cart
{
    /**
     * Sales quote repository
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Coupon factory
     *
     * @var \Magento\SalesRule\Model\CouponFactory
     */
    protected $couponFactory;

    protected $afptcRule;


    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\SalesRule\Model\CouponFactory $couponFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Unilab\Afptc\Model\Rule $afptcRule
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->couponFactory = $couponFactory;
        $this->quoteRepository = $quoteRepository;
        $this->afptcRule = $afptcRule;
    }

    /**
     * Initialize coupon
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
       
        $options = $this->getRequest()->getParam('products', null);
        if (null === $options) {
            $this->_goBack();
            return false;
        }

        $cartModel =$cart;
        $cartModel->getQuote()->collectTotals();
        foreach ($options as $optionValue) {
            if (empty($optionValue)) {
                continue;
            }
            $optionValueArray = explode(',', $optionValue);
            list($itemId, $ruleId) = $optionValueArray;

            $ruleModel = $this->afptcRule->load($ruleId);
            if (null === $ruleModel->getId()) {
                continue;
            }

            try {
                $ruleModel->apply($cartModel, $itemId);
            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
                break;
            }
        }

        if (isset($errorMessage)) {
            $this->messageManager->addError($errorMessage);
            $this->_goBack();
            return false;
        }

        $this->messageManager->addSuccess(__('Free products were added to your shopping cart.'));

        return $this->_goBack();
    }
}