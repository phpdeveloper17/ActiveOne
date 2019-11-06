<?php
/**
 * Unilab Grid List Controller.
 * @category  Unilab
 * @package   Unilab_Grid
 * @author    Unilab
 * @copyright Copyright (c) 2010-2017 Unilab Software Private Limited (https://Unilab.com)
 * @license   https://store.Unilab.com/license.html
 */
namespace Unilab\Customers\Controller\Adminhtml\Customer;


class Importresult extends \Magento\Backend\App\Action
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


    public function execute()
    {
        if(!defined('DS')){
            define('DS',DIRECTORY_SEPARATOR);
        }
        $filecsv = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'useraccount';
        $csv = file_get_contents($filecsv);
        $csv = json_decode($csv);
        $filehead = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'useraccounthead';
        $head = file_get_contents($filehead);
        $head = json_decode($head);
        $filecount = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'useraccountcount';

        $csv    = array_map("array_combine", array_fill(0, count($csv), $head), $csv);

        $fieldName = [];

        foreach ($head as $key => $value){
			
            if(strtolower($value) == 'limit'){
              $value = 'pcap_limit';			
            }
            
            $key 			= strtolower(str_replace(' ', '_', $key));		  
            $head[$key] 	= strtolower(str_replace(' ', '_', $value));
                        
            $fieldName[] = $head[$key];
            
        }
        
        $fieldName = implode(",", $fieldName);
        $csvResult = array_map("array_combine", array_fill(0, count($csv), $head), $csv);
        
        $saveTempProduct = $this->_saveTemp($csvResult);
        $records  	= $this->customerSession->getRecords();	
		$status  	= $this->customerSession->getStatussave();
		$this->customerSession->setsavecount($records['Savecount']); 
        file_put_contents($filecount,$records['Savecount']);

        $indexer = $this->indexerFactory->create();
        $indexer->load('customer_grid');
        $indexer->reindexAll();

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Import Customer Result'));
        return $resultPage;
    }
    protected function _saveTemp($csvResult){
        
        $visitorData = $this->remoteAddress->getRemoteAddress(true);
        $connection = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
      
        //**Get User Session  
        $user                   = $this->userSession->getUser(); 
        $userId                 = $this->userSession->getUserId();
        $userUsername           = $this->userSession->getUsername();

        
        $userUsername 	= 'admin';//$user->getUser()->getUsername();	
		$count 			= 0;
		$countSave		= 0;
		$countBreak 	= 10;	
		$alreadysave	= 0;
		$getData 		= array();
		$resData		= array();
		$lastIncrement	= null;
        $dataSave  		= false;

        $records		= count($csvResult);
        $dataSave = null;
		$filecount = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'useraccountcount';
        $SaveCount = file_get_contents($filecount);
        
        if(empty($SaveCount)):
			$SaveCount = 0;
        endif;

            foreach($csvResult as $_key=>$_value):
                
                $fieldValue 			= null;
                $dataHnlder				= array();	
                $dataHnlder				= null;
                $dataHnlder['username'] = $userUsername;
                if($count >= $SaveCount):
                    foreach($_value as $key=>$value):
                    
                        if ($key == 'account_id'):	
                            $dataHnlder['account_id']	= $value;	
                            $keyCode 					= $value;						
                        elseif($key == 'account_name'):	
                            $dataHnlder['account_name']	= $value;	
                        elseif($key == 'password'):	
                            $dataHnlder['password']	= $value;	
                        elseif($key == 'cap_id'):	
                            $dataHnlder['cap_id']	= $value;
                        elseif($key == 'purchase_cap'):	
                            $dataHnlder['purchase_cap']	= $value;	
                        elseif($key == 'ext'):		
                            $dataHnlder['suffix']	= $value;	
                        elseif($key == 'pcap_limit'):	
                            $dataHnlder['pcap_limit']	= $value;	
                        elseif($key == 'consumed'):		
                            $dataHnlder['consumed']	= $value;	
                        elseif($key == 'available'):		
                            $dataHnlder['available']	= $value;	
                        elseif($key == 'tender'):			
                            $dataHnlder['tender']	= $value;	
                        elseif($key == 'transaction_type'):	
                            $dataHnlder['transaction_type']	= $value;	
                        elseif($key == 'employee_id'):			
                            $dataHnlder['employee_id']	= $value;	
                        elseif($key == 'first_name'):		
                            $dataHnlder['first_name']	= $value;	
                        elseif($key == 'middle_name'):	
                            $dataHnlder['middle_name']	= $value;	
                        elseif($key == 'surname'):				
                            $dataHnlder['last_name']	= $value;	
                        elseif($key == 'last_name'):				
                            $dataHnlder['last_name']	= $value;	
                        elseif($key == 'company_code'):			
                            $dataHnlder['company_code']	= $value;	
                        elseif($key == 'price_level'):			
                            $pricename = trim($value);							
                        elseif($key == 'title'):					
                            $dataHnlder['prefix']	= $value;	
                        elseif($key == 'email'):				
                            $dataHnlder['email']	= $value;	
                        elseif($key == 'birthdate'):				
                            $dataHnlder['birthdate']	= $value;	
                        elseif($key == 'date_of_hired'):					
                            $dataHnlder['date_of_hired']	= $value;	
                        elseif($key == 'contact_number'):					
                            $dataHnlder['contact_number']	= $value;			
                        elseif($key == 'mobile'):					
                            $dataHnlder['contact_number']	= $value;	
                        elseif($key == 'refresh_period'):					
                            $dataHnlder['refresh_period']	= $value;			
                        elseif($key == 'start_date'):					
                            $dataHnlder['start_date']	= $value;			
                        elseif($key == 'refresh_date'):					
                            $dataHnlder['refresh_date']	= $value;			
                        elseif($key == 'sex'):					
                            if(strtolower($value) == 'male'):
                                $dataHnlder['gender']	= 1;	
                            elseif(strtolower($value) == 'female'):
                                $dataHnlder['gender']	= 2;	
                            endif;			
                        elseif($key == 'gender'):					
                            if(strtolower($value) == 'male'):
                                $dataHnlder['gender']	= 1;	
                            elseif(strtolower($value) == 'female'):
                                $dataHnlder['gender']	= 2;	
                            endif;
                        elseif($key == 'marital_status'):					
                            $dataHnlder['civil_status']	= $value;			
                        elseif($key == 'active'):					
                            $dataHnlder['is_active']	= $value;			
                        elseif($key == 'date_registered'):					
                            $dataHnlder['date_registered']	= $value;			
                        elseif($key == 'title'):					
                            $dataHnlder['title']	= $value;			
                        endif;						
                    endforeach;
                    ///*** Get Group ID by Company Code Begin ***////
                    $company_code 						= $dataHnlder['company_code'];
                    $sqlSelectCode 						= "SELECT customer_group_id FROM customer_group WHERE company_code='$company_code'";	
                    $AccIdresult 						= $connection->fetchRow($sqlSelectCode);
                    $dataHnlder['customer_group_id'] 	= $AccIdresult['customer_group_id'];
                    ///*** Get Group ID by Company Code End ***////
                    ///*** Get Price Level ID by Company Code Begin ***////
                    $sqlPriceLevel 						= "SELECT id FROM ".self::TABLE_PREFIX."_pricelevelmaster WHERE price_name='$pricename'";	
                    $PriceLevelResult 					= $connection->fetchRow($sqlPriceLevel);
                    $dataHnlder['price_level'] 			= $PriceLevelResult['id'];
                    if(empty($dataHnlder['price_level'])):
                        $dataHnlder['price_level']  = 0;
                    endif;
                    ///*** Get Price Level ID by Company Code End ***////			
                    if(!empty($dataHnlder['company_code'])):
                            $txnRes  	= true;
                            $txnRes = $this->_checkpcapid($dataHnlder);

                            $dataSave = true;
                            $countSave++;						
                            $currentnumber = $count + 1;
                            if($this->_isChecker($keyCode) == false && $txnRes == true):
                                $dataHnlder['count_id']	= $count;
                                $emp_id			= $dataHnlder['employee_id'];
                                $first_name		= str_replace(" ", "_", $dataHnlder['first_name']);
                                $lastname		= str_replace(" ", "_", $dataHnlder['last_name']);
                                $email 			= $dataHnlder['email'];
                                if(empty($email)):
                                    $email = strtolower($emp_id).'_'.strtolower($lastname).'@activeone.com';
                                endif;
                                try{
                                    $customer = $this->customerFactory->create();
                                    $customer->setData('firstname', $first_name);
                                    $customer->setData('lastname', $lastname);
                                    $customer->setData('email', $email);
                                    $customer->setData('middlename', $dataHnlder['middle_name']);
                                    $customer->setData('dob', date("Y-m-d", strtotime($dataHnlder['birthdate'])));
                                    $customer->setData('gender', $dataHnlder['gender']);
                                    $customer->setData('group_id', $dataHnlder['customer_group_id']);
                                    $customer->setData('price_level', $dataHnlder['price_level']);
                                    $customer->setData('employee_id', $emp_id);

                                    $customer->save();

                                    $customer_id = $customer->getId();
                                    $customerNew = $this->customerModel->load($customer_id);

                                    $customerData = $customerNew->getDataModel();
                                    $customerData->setCustomAttribute('contact_number', $dataHnlder['contact_number']);
                                    $customerData->setCustomAttribute('date_hired', date("Y-m-d H:i:s", strtotime(@$dataHnlder['date_of_hired'])));
                                    $customerData->setCustomAttribute('price_level', $dataHnlder['price_level']);
                                    $customerData->setCustomAttribute('active', 0);
                                    $customerData->setCustomAttribute('civil_status', $dataHnlder['civil_status']);
                                    $customerData->setCustomAttribute('accept_updated_privacy', 0);
                                    $customerData->setCustomAttribute('employment_status', 1);
                                    $customerData->setCustomAttribute('employee_id', $emp_id);
                                    $customerNew->updateData($customerData);

                                    $customerResource = $this->customerResourceFactory->create();
                                    $customerResource->saveAttribute($customerNew, 'contact_number');
                                    $customerResource->saveAttribute($customerNew, 'date_hired');
                                    $customerResource->saveAttribute($customerNew, 'price_level');
                                    $customerResource->saveAttribute($customerNew, 'active');
                                    $customerResource->saveAttribute($customerNew, 'civil_status');
                                    $customerResource->saveAttribute($customerNew, 'accept_updated_privacy');
                                    $customerResource->saveAttribute($customerNew, 'employment_status');
                                    $customerResource->saveAttribute($customerNew, 'employee_id');

                                    $customer = $this->customerRepository->getById($customer_id);
                                    $password = $dataHnlder['password'];
                                    if(empty($password)){
                                        $password = 'p@ssw0rd123';
                                    }
                                    $this->customerRepository->save($customer, $this->encryptor->getHash($password, true));
                                    $sqlQuery = "UPDATE customer_entity SET is_active=1, account_id='$emp_id' WHERE email='$email'";
                                    $connection->query($sqlQuery);	
                                    
                                    $sqlSelectCode 		= "SELECT entity_id FROM customer_entity WHERE email='$email'";	
                                    $AccIdresult 		= $connection->fetchRow($sqlSelectCode);

                                }catch(\Exception $e){
                                    $this->messageManager->addErrorMessage($e->getMessage());
                                    
                                }
                                $lastIncrement = @$AccIdresult['entity_id'];
                                $this->customerSession->setStatussave(1);
                                $resData[] = $currentnumber. '. '. $keyCode .' - '. $dataHnlder['account_name'].' - <span style="color:green;">Success!</span>';
                            elseif($txnRes == false && $this->_isChecker($keyCode) == false):
                                $resData[] = $currentnumber . '. '. $keyCode .' - '. $dataHnlder['account_name'].' - <span style="color:red;">Transaction Type not Exist!</span>';
                                $this->customerSession->setStatussave(0);
                            else:
                                $resData[] = $currentnumber . '. '. $keyCode .' - '. $dataHnlder['account_name'].' - <span style="color:red;">Already Exist!</span>';
                                $this->customerSession->setStatussave(0);
                            endif;
                            
                            if(!empty($lastIncrement)):
                                $dataHnlder['lastInsertId'] = $lastIncrement;						
                            
                                try{
                                    $refresh_period		= $this->_getresfreshPeriod($dataHnlder['refresh_period']);
                                    if(empty($refresh_period)):
                                        $refresh_period = $dataHnlder['refresh_period'];
                                    endif;
                                    
                                    $emp_idpre			= $dataHnlder['employee_id'];	
                                    $purchase_cap_id 	= $dataHnlder['cap_id'];
                                    $lastInsertId 		= $dataHnlder['lastInsertId'];

                                    //*** Get PCAP ID
                                    $sqlpcapID 		= "SELECT id FROM ".self::TABLE_PREFIX."_emp_purchasecap WHERE purchase_cap_id='$purchase_cap_id'";
                                    $pcapidResult 	= $connection->fetchRow($sqlpcapID);
                                    $pcapid			= $pcapidResult['id'];	

                                    $dataBenefits					 = array();
                                    $dataBenefits['employee_id']	 = $dataHnlder['employee_id'];
                                    $dataBenefits['cap_id']			 = $purchase_cap_id;
                                    $dataBenefits['lastInsertId']	 = $dataHnlder['lastInsertId'];
                                    $dataBenefits['purchase_cap_id'] = $pcapid;
                                    
                                    $start_date 	= date("Y-m-d H:i:s", strtotime($dataHnlder['start_date']));
                                    $refresh_date 	= date("Y-m-d H:i:s", strtotime($dataHnlder['refresh_date']));
                                    
                                    if(!empty($lastInsertId)):
                                        if(!empty($dataHnlder['employee_id'])):
                                            $benefitcheck = $this->_isbenefitcheck($dataBenefits);
                                            if($benefitcheck == false):
                                                if(empty($dataHnlder['consumed'])):
                                                    $dataHnlder['consumed'] = 0;
                                                endif;
                                                if(empty($dataHnlder['available'])):
                                                    $dataHnlder['available'] = 0;
                                                endif;
                                                if(empty($dataHnlder['extension'])):
                                                    $dataHnlder['extension'] = 0;
                                                endif;
                                                if(empty($dataHnlder['pcap_limit'])):
                                                    $dataHnlder['pcap_limit'] = 0;
                                                endif;
                                                
                                                $fields 						= array();
                                                $fields['purchase_cap_limit']	= $dataHnlder['pcap_limit'];
                                                $fields['entity_id']			= $lastInsertId;
                                                $fields['purchase_cap_id']		= $pcapid;
                                                $fields['available']			= $dataHnlder['available'];
                                                $fields['extension']			= $dataHnlder['extension'];
                                                $fields['emp_id']				= $dataHnlder['employee_id'];
                                                $fields['refresh_period']		= $refresh_period;
                                                $fields['start_date']			= $start_date;
                                                $fields['refresh_date']			= $refresh_date;
                                                $fields['emp_name']				= $dataHnlder['account_name'];
                                                $fields['uploadedby']			= $dataHnlder['username'];
                                                $fields['consumed']				= $dataHnlder['consumed'];
                                                $fields['created_time']			= date('Y-m-d H:i:s');
                                                $fields['update_time'] 			= date('Y-m-d H:i:s');
                                                $fields['date_uploaded']		= date('Y-m-d H:i:s');
                                                $connection->beginTransaction();
                                                $connection->insert(self::TABLE_PREFIX.'_emp_benefits', $fields);
                                                $connection->commit();
                                            endif;
                                        endif;
                                    endif;
                                }catch(\Exception $e){
                                    $this->messageManager->addErrorMessage($e->getMessage());
                                }
                                
                                $resData[] = ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size:10px;color:blue;">'. $dataHnlder['purchase_cap'] . ', Limit : '. $dataHnlder['pcap_limit']. ', Available : '. $dataHnlder['available']. ', Consumed : '. $dataHnlder['consumed']	.' - Benefit saved!</span>';
                            
                            endif;	
                            
                    endif;
                    $this->customerSession->setRecordsave($resData);
            
                endif;
            
            $count++;					
            $remainingRec  				= array();
            $remainingRec['Allrecords']	= $records;
            $remainingRec['Savecount']	= $count;		
            
            $this->customerSession->setRecords($remainingRec);						

            if($dataSave == true && $countSave == $countBreak):
                $countSave = 0;
                break;
            endif;		
        endforeach;	
    }
    
    protected function _checkpcapid($dataHnlder)
	{
        $connection         = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        
		$refresh_period		= 0;
		$start_date			= null; 
		$refresh_date		= null;
		$transaction_type 	= $dataHnlder['transaction_type'];
		$cap_id 			= $dataHnlder['cap_id'];
		$value 				= $dataHnlder['purchase_cap'];

		if((strtolower($value) == "credit") || (strtolower($value) == "debit")):	
			$sqlTrnx 		= "SELECT id FROM ".self::TABLE_PREFIX."_transaction_type WHERE transaction_name LIKE 'cash'";	
			$trnxResult 	= $connection->fetchRow($sqlTrnx);
			$tnx_id			= $trnxResult['id'];
		else:
			$sqlTrnx 		= "SELECT id FROM ".self::TABLE_PREFIX."_transaction_type WHERE transaction_name LIKE '$value'";	
			$trnxResult 	= $connection->fetchRow($sqlTrnx);
			$tnx_id			= $trnxResult['id'];
		endif;			
		
		try{
			if (empty($tnx_id)):
				return $response = false;
			else:
				$sqlCapId 		= "SELECT id FROM ".self::TABLE_PREFIX."_emp_purchasecap WHERE purchase_cap_id LIKE '$cap_id' AND tnx_id='$tnx_id'	";			
                $cap_idResult 	= $connection->fetchRow($sqlCapId);
                
				if(empty($cap_idResult['id'])):
					$fields 						= array();
					$fields['purchase_cap_des']		= $dataHnlder['purchase_cap'];
					$fields['tnx_id']				= $tnx_id;
					$fields['purchase_cap_id']		= $dataHnlder['cap_id'];
					$fields['created_time']			= date('Y-m-d H:i:s');
                    $fields['update_time'] 			= date('Y-m-d H:i:s');
                    $connection->beginTransaction();
					$connection->insert('rra_emp_purchasecap', $fields);
					$connection->commit();	
					
				endif;
				return $response = true;
			endif;
					
			
		}catch(\Exception $e){
			$this->messageManager->addErrorMessage($e->getMessage());
		}
    }	
    
    protected function _isChecker($keyCode)
	{
		$connection     = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
		$response 		= false;
		$sql 			= "SELECT entity_id FROM customer_entity WHERE account_id='$keyCode'";						
		$AccIdresult 	= $connection->fetchRow($sql);	

		if(empty($AccIdresult['entity_id'])):
			$response = false;
		else:
			$response = true;	
		endif;
		return $response;
		
    }
    
    protected function _getresfreshPeriod($refresh_period)
	{	
        $connection = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
		$unilabTypeSql 	= "SELECT option_id FROM eav_attribute_option_value WHERE value LIKE '$refresh_period'";	
		$unilabResult 	= $connection->fetchRow($unilabTypeSql);	
		return $unilabResult['option_id'];		
	}
	
	protected function _isbenefitcheck($dataBenefits)
	{
        $connection = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
		$employee_id 		= $dataBenefits['employee_id'];
		$entity_id 			= $dataBenefits['lastInsertId'];
		$purchase_cap_id 	= $dataBenefits['purchase_cap_id'];
		$unilabTypeSql 		= "SELECT id FROM ".self::TABLE_PREFIX."_emp_benefits WHERE purchase_cap_id='$purchase_cap_id' AND emp_id LIKE '$employee_id' AND entity_id='$entity_id' ";
		$unilabResult 		= $connection->fetchRow($unilabTypeSql);	
		if(!empty($unilabResult['id'])):		
			$response = true;			
		else:		
			$response = false;			
		endif;
		return 	$response;
	}
    protected function _isAllowed()
    {
        return true;
    }
}
