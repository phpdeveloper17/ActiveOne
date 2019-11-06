<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Prescription\Controller\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;

class UpdatePrescription extends \Magento\Checkout\Controller\Cart
{
   
   public function __construct(
    \Magento\Framework\Message\ManagerInterface $messageManager,
    \Magento\Framework\Escaper $escaper,
    \Magento\Framework\App\Action\Context $context,
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
    \Magento\Checkout\Model\Session $checkoutSession,
    \Magento\Store\Model\StoreManagerInterface $storeManager,
    \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
    CustomerCart $cart,
    ProductRepositoryInterface $productRepository,
    \Unilab\Prescription\Helper\Data $prescriptionHelper

    ) 
    {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart,
            $productRepository,
            $prescriptionHelper
        );
        $this->productRepository = $productRepository;
        $this->prescriptionHelper = $prescriptionHelper;
        $this->messageManager = $messageManager;
        $this->escaper = $escaper;
    }

    protected function _initProduct()
    {
        $productId = (int)$this->getRequest()->getParam('product');
        if ($productId) {
            $storeId = $this->_objectManager->get(
                \Magento\Store\Model\StoreManagerInterface::class
            )->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }

    public function execute()
    {

        try{

            $product = $this->_initProduct();
            $cart = $this->cart;

            $item_id = (int) $this->getRequest()->getParam('item_id');
            $item = $cart->getQuote()->getItemById($item_id); 

            if(($prescription = $this->prescriptionHelper->_initPrescriptions()) && $prescription instanceof \Unilab\Prescription\Model\Prescription){ 
                    $item->setPrescriptionId($prescription->getId());
            }
            else{  
                if($prescription == \Unilab\Prescription\Model\Prescription::TYPE_NONE){
                    $cart->removeItem($item_id); 
                    $message = __('Prescription is required for '.$this->escaper->escapeHtml($product->getName()).'.'); 
                }
            } 
            if(!isset($message)){
                $message = __('Prescription is updated for '.$this->escaper->escapeHtml($product->getName()).'.');
                $this->messageManager->addSuccess($message);
            }

        } catch(\Exception $e){
            $this->messageManager->addError('Cannot update shopping cart.');
        }
        return $this->_goBack();
    }
}
