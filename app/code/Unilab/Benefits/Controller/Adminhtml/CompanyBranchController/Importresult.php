<?php
/**
 * @category  Unilab
 * @package   Unilab_Benefits
 * @author    Kristian Claridad
 */
namespace Unilab\Benefits\Controller\Adminhtml\CompanyBranchController;

class Importresult extends \Magento\Backend\App\Action
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
        \Magento\Backend\Model\Auth\Session $userSession,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\App\Filesystem\DirectoryList $directorylist,
        \Unilab\Catalog\Model\Adminhtml\Addproducts $addproductsImport
    ) {
        
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->coreRegistry = $coreRegistry;
        $this->userSession = $userSession;
        $this->_coreSession = $coreSession;
        $this->_directorylist = $directorylist;
        $this->addproductsImport = $addproductsImport;
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
        $filecsv = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'companyaddress';
        $csv = file_get_contents($filecsv);
        $filehead = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'companyaddresshead';
        $head = file_get_contents($filehead);
        $filecount = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'companyaddresscount';
        $dataArr            = array();      
        $dataArr['csv']     = json_decode($csv);
        $dataArr['head']    = json_decode($head);           
        
        $SaveData = $this->_objectManager->create("\Unilab\Benefits\Model\CompanyBranchHandler")->addData($dataArr)->processData();
        $records  	= $this->_coreSession->getRecords();
     
		$status  	= $this->_coreSession->getStatussave();
		$this->_coreSession->setsavecount($records['Savecount']); 
		file_put_contents($filecount,$records['Savecount']);

        $this->userSession->setData('resData',$SaveData);
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Import Company Address Result'));
        $resultPage->getLayout()->createBlock('\Unilab\Benefits\Block\Adminhtml\CompanyBranch\Importresult');
        
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return true;
    }
}
