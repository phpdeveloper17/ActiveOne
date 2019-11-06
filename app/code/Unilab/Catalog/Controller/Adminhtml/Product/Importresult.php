<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Catalog\Controller\Adminhtml\Product;

use Magento\Customer\Controller\RegistryConstants;

class Importresult extends \Magento\Backend\App\Action
{
    /**
     * Initialize current group and set it in the registry.
     *
     * @return int
     */
   

    /**
     * Edit or create customer group.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected $customerGroupFactory;
    protected $resourceConnection;
    protected $userSession;
    protected $messageManager;
    protected $addproductsImport;

    
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Unilab\Grid\Model\customerGroupFactory $customerGroupFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Backend\Model\Auth\Session $userSession,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\App\Filesystem\DirectoryList $directorylist,
        \Unilab\Catalog\Model\Adminhtml\Addproducts $addproductsImport
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resourceConnection = $resourceConnection;
        $this->userSession = $userSession;
		$this->messageManager = $messageManager;
		$this->_coreSession = $coreSession;
        $this->_directorylist = $directorylist;
        $this->addproductsImport = $addproductsImport;
    }

    public function execute()
    {
        if(!defined('DS')){
            define('DS',DIRECTORY_SEPARATOR);
        }
        $filecsv = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'products';
        $csv = file_get_contents($filecsv);
        $filehead = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'productshead';
        $head = file_get_contents($filehead);
        $filecount = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'productscount';
        
        $dataArr 			= array();		
		$dataArr['csv'] 	= json_decode($csv);
        $dataArr['head'] 	= json_decode($head);
        
        $SaveData 	= $this->addproductsImport->addData($dataArr)->processData();
        $records  	= $this->_coreSession->getRecords();	
		$status  	= $this->_coreSession->getStatussave();
		$this->_coreSession->setsavecount($records['Savecount']); 
		file_put_contents($filecount,$records['Savecount']);
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Unilab_Catalog::product');
        $resultPage->getConfig()->getTitle()->prepend(__('Import Product Result'));
        $resultPage->addBreadcrumb(__('Catalog'), __('Import Product Result'));


        return $resultPage;
    }
}
