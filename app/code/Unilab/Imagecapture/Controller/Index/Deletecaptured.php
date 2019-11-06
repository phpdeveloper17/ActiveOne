<?php
namespace Unilab\Imagecapture\Controller\Index;

class Deletecaptured extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;
	protected $resultJsonFactory;
	protected $coreSession;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Session\SessionManagerInterface $coreSession
        )
	{
        $this->_pageFactory = $pageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->coreSession = $coreSession;
		
		return parent::__construct($context);
	}

	public function execute()
	{
        $result = $this->resultJsonFactory->create();
        $resultPage = $this->_pageFactory->create();
        $post = $this->getRequest()->getPostValue();

        try{ 
			$url_image = $post['url_img'];
			$this->coreSession->unsRxdata();				
			unlink($url_image);
			$response['file_path'] 			= $url_image;
			$response['success']   			= true; 
		
		}
		catch(Exception $e){
		
			$response['error'] = $e->getMessage();
		}	
		
		

		return $result->setData($response);
	}
}