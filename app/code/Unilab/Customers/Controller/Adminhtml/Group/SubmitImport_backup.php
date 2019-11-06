<?php
/**
 * @category  Unilab
 * @package   Unilab_Benefits->Productpricelist
 * @author    Kristian Claridad
 */
namespace Unilab\Customers\Controller\Adminhtml\Group;

class SubmitImport extends \Unilab\Customers\Controller\Adminhtml\Group
{
    const TABLE_NAME_VALUE      = 'customer_group';
    /**
     * @var \Unilab\Grid\Model\GridFactory
     */
    protected $customerGroupFactory;
    protected $resourceConnection;
    protected $userSession;
    protected $messageManager;
    protected $remoteAddress;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Unilab\Grid\Model\customerGroupFactory $customerGroupFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Customer\Model\GroupFactory $customerGroupFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Backend\Model\Auth\Session $userSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
    ) {
        parent::__construct($context);
        $this->customerGroupFactory = $customerGroupFactory;
        $this->resourceConnection = $resourceConnection;
        $this->userSession = $userSession;
        $this->messageManager = $messageManager;
        $this->remoteAddress = $remoteAddress;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue(); // get form key

        $visitorData = $this->remoteAddress->getRemoteAddress(true);
        $connection = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        // $connection->beginTransaction();


        $fullpath = $_FILES['csv_file']['tmp_name'];
        $filename = $_FILES['csv_file']['name'];
        $size = $_FILES['csv_file']['size'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        //**Get User Session  
        $user                   = $this->userSession->getUser(); 
        $userId                 = $user->getUserId();
        $userUsername           = $user->getUsername();
        $msg_stat = '';
        $disp= '';
        $data = null;
        /**
        *   Validate csv file
        */

        $csv    = array_map("str_getcsv", file($fullpath));
        $head   = array_shift($csv);
        $csv    = array_map("array_combine", array_fill(0, count($csv), $head), $csv);
        
        //Count if column is complete
        foreach ($head as $key => $value):
            if(strtolower($value) == 'id'):		  
				$value = 'price_id';			
			endif;
			  
			  $key 			= strtolower(str_replace(' ', '_', $key));		  
              $head[$key] 	= strtolower(str_replace(' ', '_', $value));
              
            if(!empty($head[$key])):
                $fieldName[] = $head[$key];
            endif;
           
        endforeach;

        $csvResult 		 = array_map("array_combine", array_fill(0, count($csv), $head), $csv);
        $fieldName 		 = implode(",", $fieldName);		
        $saveTempProduct = $this->_saveTemp($csvResult, $fieldName);
        
        // $this->_redirect('unilab_benefits/productpricelist/importresult');
      
    }
    protected function _saveTemp($csvResult, $fieldName)
	{
	
	
		$count 			= 0;
		$countSave		= 0;
		$countBreak 	= 15;	
		$alreadysave	= 0;
		$getData 		= array();
		$resData		= array();
		$tablename 		= self::TABLE_NAME_VALUE;	
		$lastIncrement	= null;
        
        $coreSession = $this->_objectManager->get('\Magento\Framework\Session\SessionManagerInterface');
		$coreSession->unsRecords();
		$records		= count($csvResult);
	
		// $filecount = $directorylist->getPath('var'). DS. 'cache'. DS . 'mage--csv';
        // $SaveCount = file_get_contents($filecount);
        // if(empty($SaveCount)):
			$SaveCount = 0;
        // endif;
		
		foreach($csvResult as $_key=>$_value):
		
			$fieldValue 					= null;
			$getData  						= null;
			$getData['companyid']			= null;
			$getData['companyname']			= null;
			$getData['tin']					= 0;
			$getData['tax_type']			= 0;
			$getData['active']				= 0;
			$getData['creditstatus']		= 0;
			$getData['term']				= 0;
			$getData['contactno']			= null;			
			$getData['contactname'] 		= null;

			if($count >= $SaveCount):
				
				foreach($_value as $key=>$value):
				
					if ($key == 'companyid'):
						$getData['companyid'] = $value;
						$keyCode = $getData['companyid'];
					elseif ($key == 'companyname'):
						$getData['companyname'] = $value;
					elseif ($key == 'tin'):
						$getData['tin'] = $value;
					elseif ($key == 'tax_type'):
						$tax_type = $value;
					elseif ($key == 'active'):
						if(strtolower($value) == 'yes'){
							$getData['active'] =1;						
						}else{
							$getData['active'] =0;
						}
					elseif ($key == 'creditstatus'):
						if(strtolower($value) == 'clear' || strtolower($value) == 'cleared'){
							$getData['creditstatus']  = 0;
						}else{
							$getData['creditstatus']  = 1;
						}
					elseif ($key == 'term'):
						$getData['term'] = $value;
					elseif ($key == 'contactno'):
						$getData['contactno'] = $value;
					elseif ($key == 'contactname'):
						$getData['contactname'] = $value;
					endif;

					$fieldValue[] = "'".$value."'";	
					
                endforeach;
                echo "<pre>";
                    print_r($fieldValue);
                echo "</pre>";
                exit();
				if(!empty($keyCode)):
					$dataSave = true;
					$countSave++;
					$currentnumber = $count + 1;
					if ($count >= $SaveCount):
								
                        if($this->_isChecker($keyCode) == false):
                            $getData['tax_type'] 		= $this->_gettaxclass($tax_type);
						
                            if(empty($getData['tax_type'])):
                            
                                $resData[] = $getData['companyname'].' - There is an error in your TAX Type. Please double check your TAX Type. - <span style="color:red;">Error!</span>';	
                                $getData['tax_type'] = 0;
                                
                            endif;
							$fieldValue = implode(",", $fieldValue);
							// $sql 		= "INSERT INTO $tablename ($fieldName) VALUES ($fieldValue)";		
                            // $this->_getConnection()->Query($sql);
                            $this->_saveData($getData);
							$resData[] = $currentnumber. '. '. $getData['pricelist_id']  .' : '. $keyCode .' - '.$getData['product_name'].' - <span style="color:green;">Success!</span>';
							$coreSession->setStatussave(1);
						else:
							$resData[] = $currentnumber. '. '. $getData['pricelist_id']  .' : '. $keyCode .' - '.$getData['product_name'].' - <span style="color:red;">Exist!</span>';
							$coreSession->setStatussave(0);
						endif;
					endif;
						
					$coreSession->setRecordsave($resData);
				endif;
			endif;
			$count++;					
			$remainingRec  				= array();
			$remainingRec['Allrecords']	= $records;
			$remainingRec['Savecount']	= $count;		
			$coreSession->setRecords($remainingRec);							
			if($dataSave == true && $countSave == $countBreak):
				$countSave = 0;
				break;
			endif;	
		endforeach;	
		
		return $this;
	}
    protected function _getConnection()
    {
		$connection = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        return $connection;
    }
    protected function _gettaxclass($tax_type)
	{	
		$unilabTypeSql 		= "SELECT class_id FROM tax_class WHERE class_name LIKE '$tax_type'";						
		$unilabResult 	= $this->_getConnection()->fetchRow($unilabTypeSql);	
		
		return $unilabResult['class_id'];		
	}	
    protected function _isChecker($keyCode)
	{
		$sql 				= "SELECT * FROM customer_group WHERE company_code='$keyCode'";						
		$AccIdresult 		= $this->_getConnection()->fetchAll($sql);					
		$total_rows 		= count($AccIdresult);	
		if($total_rows == 0):
			$response = false;
		else:
			$response = true;		
		endif;
		return $response;
    }
    protected function _saveData($getData)
	{
				
		try {
		
			$this->customerGroupFactory->setData(
				 array('customer_group_code' => $getData['companyname'],
				 'company_code' => $getData['companyid'],
				 'tax_class_id' => $getData['tax_type'],
				 'is_active' => $getData['active'],
				 'credit_status' => $getData['creditstatus'],
				 'company_terms' => $getData['term'],
				 'company_tin' => $getData['tin'],
				 'contact_person' => $getData['contactname'],
				 'contact_number' => $getData['contactno']
				 ))->save(); 		
			
			}catch(Exception $e){
			
				$this->coreSession->addError($e->getMessage());
			}	
				
					
	}
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }

}
