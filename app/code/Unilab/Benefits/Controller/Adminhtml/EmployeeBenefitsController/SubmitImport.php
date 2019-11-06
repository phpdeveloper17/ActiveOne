<?php

/**
 * Grid Admin Cagegory Map Record Save Controller.
 * @category  Unilab
 * @package   Unilab_Grid
 * @author    Unilab
 * @copyright Copyright (c) 2010-2016 Unilab Software Private Limited (https://Unilab.com)
 * @license   https://store.Unilab.com/license.html
 */
namespace Unilab\Benefits\Controller\Adminhtml\EmployeeBenefitsController;

class SubmitImport extends \Magento\Backend\App\Action
{
    /**
     * @var \Unilab\Grid\Model\GridFactory
     */
    var $gridFactory;
    protected $resourceConnection;
    protected $userSession;
    protected $messageManager;
    protected $remoteAddress;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Unilab\Grid\Model\GridFactory $gridFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Unilab\Benefits\Model\EmployeeBenefitFactory $gridFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Backend\Model\Auth\Session $userSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\App\Filesystem\DirectoryList $directorylist
    ) {
        parent::__construct($context);
        $this->gridFactory = $gridFactory;
        $this->resourceConnection = $resourceConnection;
        $this->userSession = $userSession;
        $this->messageManager = $messageManager;
        $this->remoteAddress = $remoteAddress;
        $this->_coreSession = $coreSession;
        $this->_directorylist = $directorylist;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        if(!defined('DS')){
            define('DS',DIRECTORY_SEPARATOR);
        }
        $data = $this->getRequest()->getPostValue(); // get form key

        $visitorData = $this->remoteAddress->getRemoteAddress(true);
        $connection = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);

        $fullpath = $_FILES['csv_file']['tmp_name'];
        $filename = $_FILES['csv_file']['name'];
        $size = $_FILES['csv_file']['size'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if(strtolower($ext) != 'csv'){
            $this->messageManager->addErrorMessage($filename.' - Invalid file type.');
            return $this->_redirect('unilab_benefits/employeebenefitscontroller/import');
        }

        //**Get User Session  
        $user                   = $this->userSession->getUser(); 
        $userId                 = $user->getUserId();
        $userUsername           = $user->getUsername();
  
        /**
        *   Validate csv file
        */
        try{
            
            $csv    = array_map("str_getcsv", file($fullpath));
            $head   = array_shift($csv);
            
            if(file_exists($this->_directorylist->getPath('var'). DS. 'cache'. DS . 'mage--csv') === FALSE) {
                mkdir($this->_directorylist->getPath('var'). DS. 'cache'. DS . 'mage--csv');
            }
            $filecsv = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'empbenefit';
            $filehead = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'empbenefithead';
            $filecount = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'empbenefitcount';
            file_put_contents($filecsv, json_encode($csv));
            file_put_contents($filehead, json_encode($head));
            file_put_contents($filecount, json_encode(0));

            //Count if column is complete
            $fieldName = [];
            $hiddenOrEmptyHead = false;
            foreach ($head as $key => $value):
                if(!empty($head[$key])):
                    $fieldName[] = $head[$key];
                else:
                    $hiddenOrEmptyHead = true;
                endif;
            endforeach;
            $required_fields = ['emp_id','purchase_cap_name','purchase_cap_limit','consumed','extension','refresh_period','start_date','refresh_date','available'];
            $missing_fields = [];

            foreach($required_fields as $required_field){
                if(!in_array($required_field, $fieldName)){
                    $missing_fields[] = $required_field;
                }
            }
            $resultRedirect = $this->resultRedirectFactory->create();
           
            $countArray = [];
			foreach($csv as $c => $v){
				$countArray[$c] = array_count_values($v);
			}
            if(empty($csv) || @$countArray[0][''] > 4){
                $this->messageManager->addErrorMessage("Empty/insufficient details please check");
                $resultRedirect->setPath('*/*/import');
                return $resultRedirect;
            }
            if($hiddenOrEmptyHead || count($head) > count($required_fields) ||count($csv[0]) > count($required_fields)){
                $this->messageManager->addErrorMessage("Please remove any invalid or hidden characters in csv file");
                $resultRedirect->setPath('*/*/import');
                return $resultRedirect;
            }
            if(count($missing_fields) > 0){
                $this->messageManager->addErrorMessage("The following fields are missing: " . implode(', ',array_unique($missing_fields)). ". Please try again.");
                $resultRedirect->setPath('*/*/import');
                return $resultRedirect;
            }
        }catch(\Exception $e){
            $this->messageManager->addError($e->getMessage());
        }
        return $this->_redirect('unilab_benefits/employeebenefitscontroller/importresult');
    }
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
