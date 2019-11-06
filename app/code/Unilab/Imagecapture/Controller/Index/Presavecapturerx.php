<?php

namespace Unilab\Imagecapture\Controller\Index;

class Presavecapturerx extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;
    protected $resultJsonFactory;
    protected $mediaDirectory;
    protected $storeManager;
    protected $coreSession;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Session\SessionManagerInterface $coreSession
        )
	{
        $this->mediaDirectory = $fileSystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_pageFactory = $pageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->storeManager = $storeManager;
        $this->coreSession = $coreSession;
		
		return parent::__construct($context);
	}

	public function execute()
	{
		ini_set('post_max_size','200M');
		ini_set('upload_max_filesize','210M');
		ini_set('client_max_body_size ','210M');
		ini_set('post_max_size','200MB');
		ini_set('upload_max_filesize','210MB');
		ini_set('client_max_body_size ','210MB');
        $result = $this->resultJsonFactory->create();
        $resultPage = $this->_pageFactory->create();
        $post = $this->getRequest()->getPostValue();

        try{ 		
            
			$path = $this->mediaDirectory->getAbsolutePath('tmp/prescriptions/');
            $path = str_replace('pub/', '', $path);

			$image_name 	= $post['prodid'];
			$file_name  	= date("Y_M_D_his");
			$extension 		= $post['img_type']; 
			
			$scanned_rx 	= $image_name.'_'.$file_name.'.'.$extension;						
			$img_url 		= $post['imgdata'];
			
			$savefile 		= $path.$scanned_rx;

			if(!is_dir($path)){
				mkdir($path);
			}

			file_put_contents($savefile, file_get_contents($img_url));						
			
			$burl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
			$burl = str_replace('pub/','',$burl);			

			$prescription_data 	= array();
			$prescription_data['prod_id'] 			= $post['prodid'];
			$prescription_data['capture_rx'] 		= $burl."tmp/prescriptions/".$scanned_rx;
			$prescription_data['origimage_name'] 	= $burl."tmp/prescriptions/".$scanned_rx;

			
			$this->coreSession->setRxdata($prescription_data);								
			
			
 
			$response['file_path']   		= $savefile; 
			$response['success']   			= true; 
			$response['prescription_data'] 	= $this->coreSession->getRxdata();		
		
		}
		catch(Exception $e){
		
			$response['error'] = $e->getMessage();
		}

		return $result->setData($response);
	}
}