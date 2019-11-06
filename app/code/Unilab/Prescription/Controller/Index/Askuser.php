<?php
namespace Unilab\Prescription\Controller\Index;

class Askuser extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;
	protected $resultJsonFactory;
	protected $_productloader;
	protected $_helper;
	protected $layoutFactory;
	protected $checkoutSession;
	protected $customerSession;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Catalog\Model\ProductFactory $_productloader,
		\Unilab\Prescription\Helper\Data $helper,
		\Magento\Framework\View\LayoutFactory $layoutFactory,
		\Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Customer\Model\Session $customerSession
        )
	{
        $this->_pageFactory = $pageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_productloader = $_productloader;
		$this->_helper = $helper;
		$this->layoutFactory = $layoutFactory;
		$this->checkoutSession = $checkoutSession;
		$this->customerSession = $customerSession;
		
		return parent::__construct($context);
	}

	public function execute()
	{
        $result = $this->resultJsonFactory->create();
        $resultPage = $this->_pageFactory->create();
        $post = $this->getRequest()->getPostValue();

        $product = $this->_productloader->create()->load($post['product']);; 
		
		if (! $this->customerSession->isLoggedIn()) {
			 $current_session = $this->customerSession;
			 if(isset($current_session)) $current_session->setData("before_auth_url", $product->getProductUrl()); 
		} 

        if(!$this->getRequest()->isAjax()){
        	$this->_redirect('*/*/');
        }  		
		 
		$response  = array('proceed_to_cart' => false); 				
		
		// $this->_view->loadLayout(); 
		
		/** Check if product exists and (product already in cart or product requires no prescription) **/
		if($product->getId() && $this->_helper->isProductInCart($product)){			
			$response['proceed_to_cart'] = true; 			
		}
		else{		

			$askuser_dialog_block = $resultPage->getLayout()->createBlock('Unilab\Prescription\Block\Askuser');
			$askuser_dialog_block->setTemplate('Unilab_Prescription::askuser.phtml');
			$cancel_dialog_block = $resultPage->getLayout()->createBlock('Unilab\Prescription\Block\Cancel');
			$cancel_dialog_block->setTemplate('Unilab_Prescription::cancel.phtml');
			$cancel_trans_dialog_block	= $resultPage->getLayout()->createBlock('Unilab\Prescription\Block\Canceltransaction');
			$cancel_trans_dialog_block->setTemplate('Unilab_Prescription::cancel_transaction.phtml');
			$prescription_dialog_block	= $resultPage->getLayout()->createBlock('Unilab\Prescription\Block\Prescription');
			$prescription_dialog_block->setTemplate('Unilab_Prescription::prescription.phtml');
			$prescription_dialog_block->setProductToCartFormFields($post);
			
			$response['askuser_dialog'] 	 = $askuser_dialog_block->toHtml();	
			$response['prescription_dialog'] = $prescription_dialog_block->toHtml();	
			$response['cancel_dialog']	     = $cancel_dialog_block->toHtml();	
			$response['cancel_trans_dialog'] = $cancel_trans_dialog_block->toHtml(); 	
	
		}

		return $result->setData($response);
	}
}