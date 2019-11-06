<?php

/**
 * Grid Admin Cagegory Map Record Save Controller.
 * @category  Unilab
 * @package   Unilab_Grid
 * @author    Unilab
 * @copyright Copyright (c) 2010-2016 Unilab Software Private Limited (https://Unilab.com)
 * @license   https://store.Unilab.com/license.html
 */
namespace Unilab\Customers\Controller\Adminhtml\Customer;

class SubmitImport extends \Magento\Backend\App\Action
{
    
    protected $customerFactory;
    protected $resourceConnection;
    protected $customerSession;
    protected $userSession;
    protected $messageManager;
    protected $remoteAddress;
    protected $directoryList;
    protected $requestFactory;
    protected $resultPageFactory;
    protected $customerModel;
    protected $customerResourceFactory;
    protected $indexerFactory;
    protected $encryptor;
    protected $customerRepository;
    const TABLE_PREFIX = 'rra';
    const DS = DIRECTORY_SEPARATOR;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Backend\Model\Auth\Session $userSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\App\RequestFactory $requestFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Customer $customerModel,
        \Magento\Customer\Model\ResourceModel\CustomerFactory $customerResourceFactory,
        \Magento\Indexer\Model\IndexerFactory $indexerFactory,
        \Magento\Framework\Encryption\Encryptor $encryptor,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\App\Filesystem\DirectoryList $directorylist
    ) {
        parent::__construct($context);
        $this->customerFactory = $customerFactory;
        $this->resourceConnection = $resourceConnection;
        $this->customerSession = $customerSession;
        $this->userSession = $userSession;
        $this->messageManager = $messageManager;
        $this->remoteAddress = $remoteAddress;
        $this->directoryList = $directoryList;
        $this->requestFactory = $requestFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->customerModel = $customerModel;
        $this->customerResourceFactory = $customerResourceFactory;
        $this->indexerFactory = $indexerFactory;
        $this->encryptor = $encryptor;
        $this->customerRepository = $customerRepository;
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
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*/import');
            return $resultRedirect;
        }
        //**Get User Session  
        $user                   = $this->userSession->getUser(); 
        $userId                 = $this->userSession->getUserId();
        $userUsername           = $this->userSession->getUsername();
  
        /**
        *   Validate csv file
        */
        try{
            $csv    = array_map("str_getcsv", file($fullpath));
            $head   = array_shift($csv);

            if(file_exists($this->_directorylist->getPath('var'). DS. 'cache'. DS . 'mage--csv') === FALSE) {
                mkdir($this->_directorylist->getPath('var'). DS. 'cache'. DS . 'mage--csv');
            }
            $filecsv = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'useraccount';
            $filehead = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'useraccounthead';
            $filecount = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'useraccountcount';
            file_put_contents($filecsv, json_encode($csv));
            file_put_contents($filehead, json_encode($head));
            file_put_contents($filecount, json_encode(0));

            $hiddenOrEmptyHead = false;
            $fieldName = [];
            foreach ($head as $key => $value):
                if(strtolower($value) == 'limit'){
                    $value = 'pcap_limit';			
                }
                
                $key 			= strtolower(str_replace(' ', '_', $key));		  
                $head[$key] 	= strtolower(str_replace(' ', '_', $value));
                if(!empty($head[$key])):
                    $fieldName[] = $head[$key];
                else:
                    $hiddenOrEmptyHead = true;
                endif;
            endforeach;
     
            $required_fields = ['account_id','employee_id','account_name','title','first_name','middle_name','surname','ext','cap_id','purchase_cap','pcap_limit','extension','consumed','available','tender','transaction_type','refresh_period','start_date','refresh_date','birthdate','sex','marital_status','position_and_title','active','company_code','assigned_to_branch','memo','this_is_a_customer','this_is_a_supplier','this_is_a_cashier','this_is_an_employee','price_level','customer_type','allow_account_to_logon','password','access_level','enable_challenge_question','security_question','answer','email','billing_address_1','billing_address_2','billing_address_3','phone','mobile','fax','tin_no','updated_by','updated_date'];
            $missing_fields = [];
            foreach($required_fields as $required_field){
                if(!in_array($required_field, $fieldName)){
                    $missing_fields[] = $required_field;
                }
            }
			$countArray = [];
			foreach($csv as $c => $v){
				$countArray[$c] = array_count_values($v);
			}
			
            $resultRedirect = $this->resultRedirectFactory->create();
			
			
            if(empty($csv) || @$countArray[0][''] > 20){
                $this->messageManager->addErrorMessage("Empty details please check");
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
    return $this->_redirect('*/*/importresult');
    }
   
}
