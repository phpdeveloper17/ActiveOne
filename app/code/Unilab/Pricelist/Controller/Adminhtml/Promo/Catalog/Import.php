<?php

namespace Unilab\Pricelist\Controller\Adminhtml\Promo\Catalog;


class Import extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Unilab\Grid\Model\GridFactory
     */
    private $gridFactory;

    protected $resultPageFactory;

    protected $resultJsonFactory; 

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\App\Filesystem\DirectoryList $directorylist
    ) {
        
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory; 
        $this->coreRegistry = $coreRegistry;
        $this->_coreSession = $coreSession;
        $this->_directorylist = $directorylist;
        parent::__construct($context);
        
        // $this->gridFactory = $gridFactory;
    }

    /**
     * Mapped Grid List page.
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        if(!defined('DS')){
            define('DS',DIRECTORY_SEPARATOR);
        }
        $this->_coreSession->unssavecount();     
        $this->_coreSession->unsRecords();   
        $this->_coreSession->unsStatussave();
        

        $filecsv = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'pricelist';
        $filehead = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'pricelisthead';
        $filecount = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'pricelistcount';

        if(file_exists($filecsv))
            unlink($filecsv);
        if(file_exists($filehead))
            unlink($filehead);
        if(file_exists ($filecount))
            unlink($filecount);
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Import Price List'));
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Unilab_Pricelist::import');
    }
}
