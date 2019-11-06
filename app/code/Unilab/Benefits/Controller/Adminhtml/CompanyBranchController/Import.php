<?php
/**
 * Unilab Grid List Controller.
 * @category  Unilab
 * @package   Unilab_Grid
 * @author    Unilab
 * @copyright Copyright (c) 2010-2017 Unilab Software Private Limited (https://Unilab.com)
 * @license   https://store.Unilab.com/license.html
 */
namespace Unilab\Benefits\Controller\Adminhtml\CompanyBranchController;

// use Magento\Framework\Controller\ResultFactory;
// use Magento\Framework\App\Action\Context;
// use Magento\Framework\View\Result\PageFactory;
// use Magento\Framework\Controller\Result\JsonFactory;

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

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Unilab\Grid\Model\GridFactory $gridFactory
     */
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
        // defined('_JEXEC') or die('Restricted access');
        if(!defined('DS')){
            define('DS',DIRECTORY_SEPARATOR);
        }
        $this->_coreSession->unssavecount();     
        $this->_coreSession->unsRecords();   
        $this->_coreSession->unsStatussave();
        //https://alanstorm.com/magento-2-base-directories/

        $filecsv = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'companyaddress';
        $filehead = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'companyaddresshead';
        $filecount = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'companyaddresscount';

        if(file_exists($filecsv))
            unlink($filecsv);
        if(file_exists($filehead))
            unlink($filehead);
        if(file_exists ($filecount))
            unlink($filecount);   
        

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Import Company Address'));
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Unilab_Benefits::import_company_branch');
    }
}
