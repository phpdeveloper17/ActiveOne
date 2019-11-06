<?php
namespace Unilab\Prescription\Controller\Index;

class Editprescription extends \Magento\Framework\App\Action\Action
{
	
	protected $messageManager;
	protected $directoryList;
	protected $uploaderFactory;
	protected $mediaDirectory;
	protected $storeManager;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\App\Filesystem\DirectoryList $directoryList,
		\Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
		\Magento\Framework\Filesystem $fileSystem,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Checkout\Model\Session $checkoutSession,
		\Unilab\Prescription\Model\PrescriptionFactory $prescriptionFactory
        )
	{
		$this->mediaDirectory = $fileSystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_pageFactory = $pageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->messageManager = $messageManager;
        $this->directoryList = $directoryList;
        $this->uploaderFactory = $uploaderFactory;
		$this->storeManager = $storeManager;
		$this->checkoutSession = $checkoutSession;
        $this->prescriptionFactory = $prescriptionFactory;
		
		return parent::__construct($context);
	}

	public function execute()
	{
		
		$result = $this->resultJsonFactory->create();
		$resultPage = $this->_pageFactory->create();
		
		if (!$this->getRequest()->isXmlHttpRequest()) {
			return ;
		}
		try{
			$quote 			= $this->checkoutSession->getQuote();
			$data   		= $this->getRequest()->getPost();
			$item  			= $quote->getItemById($data['item_id']);
			$prescription 	= $this->prescriptionFactory->create()->load($item->getData('prescription_id'));
			$params 		= $this->getRequest()->getPost();
		
			$params = array_merge(
				$params->toArray(),
				array('form_action' => $this->storeManager->getStore()->getBaseUrl().'prescription/cart/updateprescription',
				array('product' =>$item->getProductId()),
				'product' => $item->getProductId())
			);
			$cancel_dialog_block = $resultPage->getLayout()->createBlock('Unilab\Prescription\Block\Cancel');
			$cancel_dialog_block->setTemplate('Unilab_Prescription::cancel.phtml');
			$cancel_trans_dialog_block	= $resultPage->getLayout()->createBlock('Unilab\Prescription\Block\Canceltransaction');
			$cancel_trans_dialog_block->setTemplate('Unilab_Prescription::cancel_transaction.phtml');
			$prescription_dialog_block	= $resultPage->getLayout()->createBlock('Unilab\Prescription\Block\Prescription');
			$prescription_dialog_block->setTemplate('Unilab_Prescription::prescription.phtml');
			$prescription_dialog_block->setProductToCartFormFields($params);

			// $response['askuser_dialog'] 	 = $askuser_dialog_block->toHtml();	
			$response['prescription_dialog'] = $prescription_dialog_block->toHtml();	
			$response['cancel_dialog']	     = $cancel_dialog_block->toHtml();	
			$response['cancel_trans_dialog'] = $cancel_trans_dialog_block->toHtml();

		}catch(Exception $e){
			$response['error'] = $e->getMessage();
		}
 
		return $result->setData($response);
	}
}