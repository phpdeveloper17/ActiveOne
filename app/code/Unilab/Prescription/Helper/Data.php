<?php
namespace Unilab\Prescription\Helper;

use \Magento\Checkout\Model\Session as CheckoutSession;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{   

    protected $checkoutSession;
	protected $prescriptionFactory;
	protected $messageManager;
	protected $storeManager;
	protected $directoryList;
	protected $customerSession;
	protected $mediaDirectory;
	protected $_logger;
	protected $request;
	protected $uploaderFactory;

	const DS = DIRECTORY_SEPARATOR;

	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		CheckoutSession $checkoutSession,
		\Unilab\Prescription\Model\PrescriptionFactory $prescriptionFactory,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\Filesystem\DirectoryList $directoryList,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\Filesystem $fileSystem,
		\Magento\Framework\App\RequestInterface $request,
		\Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
		) 
	{
		
		$this->mediaDirectory = $fileSystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
		$this->checkoutSession = $checkoutSession;
		$this->prescriptionFactory = $prescriptionFactory;
		$this->messageManager = $messageManager;
		$this->storeManager = $storeManager;
		$this->directoryList = $directoryList;
		$this->customerSession = $customerSession;
		$this->request = $request;
		$this->uploaderFactory = $uploaderFactory;
		$this->_pageFactory = $pageFactory;
        $this->resultJsonFactory = $resultJsonFactory;

		return parent::__construct($context);
    }

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getCustomAttr($_product, $field, $store = 0){
        return $_product->getResource()->getAttributeRawValue($_product->getId(),$field,$store);
    }

    protected function _getCart()
    {
        return $this->checkoutSession;
    } 
	
    protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }
	
	public function isProductInCart($_product)
	{  
		$isProductInCart = false; 
		$quote		     = $this->_getQuote();
		
		if($quote->hasItems()){ 
			foreach($quote->getAllItems() as $_item) {
				if($_item->getProductId() == $_product->getId()){
					$isProductInCart = $_item;
					break;
				}
			}
		}
		
		return $isProductInCart;
	}

	public function _getModel()
	{
		return $this->prescriptionFactory->create();
	}
	
	public function _initPrescriptions()
	{  
		
        $files  			 = $_FILES;		
		$prescription  		 = false;	 
		$prescription_params = $this->request->getPost('prescription');		 
		$prescription_type   = $prescription_params['type'];	 
		$saveImageCap = $this->_saveImageCapture($prescription_params);
		
		//$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/prescriptionparamslogs');
        //$logger = new \Zend\Log\Logger();
        //$logger->addWriter($writer);
		//$logger->info($prescription_params);
		
		$imgprescrip = [];
		if($saveImageCap['success']){
			$imgprescrip = ['scanned_rxs'=>[$saveImageCap['orig_image_name']=>$saveImageCap['image_name']]];
		}
		
		try{
			if(count($prescription_params) > 0){
				$prescription_model = $this->_getModel();
				$prescription_params['customer_id'] = $this->customerSession->getCustomer()->getId();
				 
				if(isset($prescription_params['prescription_id']) && !empty($prescription_params['prescription_id'])){
					$prescription_model =	$prescription_model->load($prescription_params['prescription_id']);
				}
				
				switch($prescription_type)
				{			
					case \Unilab\Prescription\Model\Prescription::TYPE_NEW :		
							if(!$prescription_model->getId()){
								unset($prescription_params['prescription_id']);
							}
							$prescription_params['scanned_rx'] = "";
							
							$prescription = $prescription_model->setData($prescription_params)
															   ->save(); 
						break;
					case \Unilab\Prescription\Model\Prescription::TYPE_PHOTO :	
							try {	
							
								
								$scanned_rx_files 	 = array();
								$original_filenames	 = array();
								$path 	 			 = $this->mediaDirectory->getAbsolutePath('prescriptions/');								
								$temp_path 			 = $this->mediaDirectory->getAbsolutePath('tmp/prescriptions/'); 

								$path = str_replace('pub/','',$path);
								$temp_path = str_replace('pub/','',$temp_path);

								if(!is_dir($path)){
									mkdir($path);
								}
								//image capture
								if(!empty($imgprescrip)){
									if(isset($imgprescrip['scanned_rxs']) && count($imgprescrip['scanned_rxs']) > 0){										
										foreach($imgprescrip['scanned_rxs'] as $orig_filename => $rx_name){
											if(file_exists($temp_path . $rx_name)){ 
												rename($temp_path.$rx_name,$path.$rx_name);
												$scanned_rx_files[] = $rx_name; 
											}elseif(file_exists($path . $rx_name)){ 
												$scanned_rx_files[] = $rx_name; 
											}
											$original_filenames[]   = $orig_filename;
											
										}
									}
								}else{
									if(isset($prescription_params['scanned_rxs']) && count($prescription_params['scanned_rxs']) > 0){										
										foreach($prescription_params['scanned_rxs'] as $orig_filename => $rx_name){
											if(file_exists($temp_path . $rx_name)){ 
												rename($temp_path.$rx_name,$path.$rx_name);
												$scanned_rx_files[] = $rx_name; 
											}elseif(file_exists($path . $rx_name)){ 
												$scanned_rx_files[] = $rx_name; 
											}
											$original_filenames[]   = $orig_filename;
											
										}
									}
								}
								if(count($scanned_rx_files) > 0 ) {
									$prescription = $prescription_model->setData('scanned_rx',implode(",",$scanned_rx_files))
																	   ->setData('original_filename',implode(",",$original_filenames))
																	   ->setData('customer_id',$this->customerSession->getCustomer()->getId())
																	   ->setDatePrescribed($prescription_params['date_prescribed_2'])
																	   ->save(); 
									//update filenames
									$scanned_prescription = explode(",",$prescription->getScannedRx());
									$new_rx_formatted     = array();
									foreach($scanned_prescription as $scanned_rx){
										$extension     = str_replace('.', '', strrchr($scanned_rx, '.')); 	
										$new_file_name = $prescription->getId().'-'.md5(strtoupper($scanned_rx)).'.'.$extension;
										rename($path.$scanned_rx,$path.$new_file_name);
										$new_rx_formatted[] = $new_file_name;
									}
									$prescription->setScannedRx(implode(",",$new_rx_formatted))->save(); 
								} 
									
							} catch (\Exception $e) { die($e->getMessage());
								$this->messageManager->addError($e->getMessage());
							} 					
						break;
					case \Unilab\Prescription\Model\Prescription::TYPE_EXISTING:
							if(!isset($prescription_params['prescription_id'])){
								$this->messageManager->addError('No Prescription Selected');
							}
							$prescription = $this->_getModel()->load($prescription_params['prescription_id']);							
						break;
					case \Unilab\Prescription\Model\Prescription::TYPE_NONE: 
							$prescription = Unilab\Prescription\Model\Prescription::TYPE_NONE;			
						break;
					default: 
							$prescription = false;
						break;
				}
			}			
		}catch(\Exception $e){ 
			$this->messageManager->addError($e->getMessage());
		}
		
		return $prescription;		
	}
	public function _saveImageCapture($prescription_params){
		$result = $this->resultJsonFactory->create();
		$resultPage = $this->_pageFactory->create();
		$path = $this->mediaDirectory->getAbsolutePath('tmp/prescriptions/'); 
		$path = str_replace('pub/','',$path);
		
		if(!empty($prescription_params['scanned_rx']['prescription_scanned_rx_path'])){
			$filepathCurrent = $prescription_params['scanned_rx']['prescription_scanned_rx_path'];
			$filenameImgCap = explode('/',$prescription_params['scanned_rx']['prescription_scanned_rx_path']);
			$indexF = (count($filenameImgCap)-1);
			$filenameImgCap = $filenameImgCap[$indexF];
			
			$current_rx_selected = 'prescription_scanned_rx';
			$new_file_values = array();
			$new_file_values['prescription']['name']['scanned_rx']        = $filenameImgCap;
			
			
			$file_name  = md5(date("YMDhis").$filenameImgCap);
			$newFilepath = $path.$file_name.'.PNG';
			
			if (!copy($filepathCurrent, $newFilepath)) {
				echo 'Error on Copying Image';
				$response['success']   = false;
			}
			
			$response['success']   = true; 		
			
			$response['image_name'] 		 = $file_name.'.PNG';						
			$response['orig_image_name'] 	 = $filenameImgCap;		
		}else{
			$response['success']   = false;
		}
				
		//$response['scanned_rx_list'] 	 = $preuploaded_rx_list->toHtml();
		return $response;
	}
	// public function isValidPrescription($prescription_id)
	// {  
	//     $prescription = Mage::getModel("prescription/prescription")->load($prescription_id);
		
	// 	if(!$prescription->getId() || 
	// 	   ($prescription->getStatus() == Unilab_Prescription_Model_Prescription::STATUS_INVALID) || 
	// 	   ($prescription->getConsumed()))
	// 	{
	// 		return false;
	// 	}
		
	// 	return true;
	// }
}