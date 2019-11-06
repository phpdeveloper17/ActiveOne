<?php
namespace Unilab\Prescription\Controller\Index;

class Presavescannedrx extends \Magento\Framework\App\Action\Action
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
		\Magento\Store\Model\StoreManagerInterface $storeManager
        )
	{
		$this->mediaDirectory = $fileSystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_pageFactory = $pageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->messageManager = $messageManager;
        $this->directoryList = $directoryList;
        $this->uploaderFactory = $uploaderFactory;
        $this->storeManager = $storeManager;
        
		
		return parent::__construct($context);
	}

	public function execute()
	{
		$result = $this->resultJsonFactory->create();
		$resultPage = $this->_pageFactory->create();
        try{  
			 $response			  = array('success'=> false); 
			
			 $post_parameters     = $this->getRequest()->getParams('prescription');
			 $post_data = $this->getRequest()->getPostValue();
				$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/prescriptionWebcam.log');
				$logger = new \Zend\Log\Logger();
				$logger->addWriter($writer);
				$logger->info($post_data);
			
			 $current_rx_selected = 'prescription_scanned_rx';
			 if(empty($_FILES['prescription']['size']['scanned_rx'][$current_rx_selected])){
				// throw new Exception(Mage::helper("prescription")->__("Error on file upload."));
				$this->messageManager->addError('Error on file upload.');
			 }
			 if(isset($_FILES['prescription']['name']['scanned_rx'][$current_rx_selected]) && 
					  $_FILES['prescription']['name']['scanned_rx'][$current_rx_selected] != ""){	 
					
				$path = $this->mediaDirectory->getAbsolutePath('tmp/prescriptions/'); 
				$path = str_replace('pub/','',$path);
				$new_file_values = array();
				$new_file_values['prescription']['name']['scanned_rx']        = $_FILES['prescription']['name']['scanned_rx'][$current_rx_selected];
				$new_file_values['prescription']['type']['scanned_rx']        = $_FILES['prescription']['type']['scanned_rx'][$current_rx_selected];
				$new_file_values['prescription']['tmp_name']['scanned_rx']    = $_FILES['prescription']['tmp_name']['scanned_rx'][$current_rx_selected];
				$new_file_values['prescription']['error']['scanned_rx']       = $_FILES['prescription']['error']['scanned_rx'][$current_rx_selected];
				$new_file_values['prescription']['size']['scanned_rx']        = $_FILES['prescription']['size']['scanned_rx'][$current_rx_selected];
				
				//VALIDATE FIRST FILESIZE
				$image_size = ($new_file_values['prescription']['size']['scanned_rx']/1024)/1024;
				
				if(!in_array($new_file_values['prescription']['type']['scanned_rx'],array('image/jpeg', 'image/jpg','image/pjpeg','image/png','image/gif','image/x-png'))){
					$response['error']   = 'TYPE';  
				}elseif($image_size > \Unilab\Prescription\Model\Prescription::DEFAULT_RX_IMG_SIZE || $image_size == 0){ 
					$response['error'] = 'SIZE'; 
				}
				
				if(!isset($response['error'])){ 
					$_FILES   = $new_file_values; 
					// $uploader = new Varien_File_Uploader('prescription[scanned_rx]');
					$uploader = $this->uploaderFactory->create(['fileId' => 'prescription[scanned_rx]']);
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					$uploader->setFilesDispersion(false);
					
					$extension = $uploader->getFileExtension(); 
					$file_name  = md5(date("YMDhis").$_FILES['prescription']['name']['scanned_rx']);									
					$scanned_rx = $uploader->save($path, $file_name.'.'.$extension); 
					
					//SAVE TO RX TEMP DB ---> Cancelled (No possible quote id on first submission of RXs 
					//$prescription_temp_rxs = Mage::getModel("prescription/prescription_temp_rx");
					//$prescription_temp_rxs->setQuoteId($this->getQuote()->getId())->save(); 
					$burl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
					$burl = str_replace('pub/','',$burl);
					$response['file_path'] =  $burl."tmp/prescriptions/".$scanned_rx['file'];
					//$response['file_path'] =  Mage::getUrl("media/tmp/prescriptions/").$scanned_rx['file'];
					$response['success']   = true; 		
					// $this->loadLayout(); 
					$preuploaded_rx_list  = $resultPage->getLayout()->createBlock('Unilab\Prescription\Block\Presavescannedrx');
					$preuploaded_rx_list->setTemplate('Unilab_Prescription::newuploaded.phtml');
					$preuploaded_rx_list->setImageName($scanned_rx['file']);
					$preuploaded_rx_list->setImageSource($response['file_path']);
																	
					$response['image_name'] 		 = $scanned_rx['file'];						
					$response['orig_image_name'] 	 = $new_file_values['prescription']['name']['scanned_rx'];				
					$response['scanned_rx_list'] 	 = $preuploaded_rx_list->toHtml();								
				}
			}	  		
		
		}catch(Exception $e){
			$response['error'] = $e->getMessage();
		}
 
		return $result->setData($response);
	}
}