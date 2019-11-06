<?php
namespace Unilab\Benefits\Model;

use Unilab\Benefits\Api\Data\CompanyBranchInterface;

// class CompanyBranch extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
class CompanyBranch extends \Magento\Framework\Model\AbstractModel implements CompanyBranchInterface
{
	const CACHE_TAG = 'rra_company_branches';
    const TABLE_NAME_VALUE = 'rra_company_branches';
    // const ID = 'id';
    // const EMPLOYEE_ID = 'emp_id';
    // const GROUP_ID = 'group_id';
    // const ENTITY_ID = 'entity_id';
    // const EMPLOYEE_NAME = 'emp_name';
    // const PURCHASE_CAP_LIMIT = 'purchase_cap_limit';
    // const PURCHASE_CAP_ID = 'purchase_cap_id';
    // const CONSUMED = 'consumed';
    // const AVAILABLE = 'available';
    // const EXTENSION = 'extension';
    // const REFRESH_PERIOD = 'refresh_period';
    // const START_DATE = 'start_date';
    // const REFRESH_DATE = 'refresh_date';
    // const IS_ACTIVE = 'is_active';
    // const CREATED_AT = 'created_time';
    // const UPDATE_AT = 'update_time';
    // const UPLOADED_BY = 'uploadedby';
    // const DATE_UPLOADED = 'date_uploaded';

	protected $_cacheTag = 'rra_company_branches';

	protected $_eventPrefix = 'rra_company_branches';

	protected function _construct()
	{
		$this->_init('Unilab\Benefits\Model\ResourceModel\CompanyBranch');
	}

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\App\Filesystem\DirectoryList $directorylist,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_resourceConnection = $resourceConnection;
        $this->_coreSession = $coreSession;
        $this->_directorylist = $directorylist;
        $this->_logger = $logger;
    } 

    // public function getIdentities()
    // {
    //     return [self::CACHE_TAG . '_' . $this->getId()];
    // }
	/**
    * Get Id.
    *
    * @return int
    */
    public function getId() 
    {
        return $this->getData(self::ID);
    }

    public function setId($id) 
    {
        return $this->setData(self::ID, $id);
    }

    /**
    * Get EmployeeId.
    *
    * @return var
    */
    public function getCompanyId()
    {
        return $this->getData(self::COMPANY_ID);
    }
    /**
    * Set EmpId.
    */
    public function setCompanyId($emp_id)
    {
        return $this->setData(self::COMPANY_ID, $emp_id);
    }

    /**
    * Get GroupId.
    *
    * @return int
    */
    public function getContactPerson()
    {
        return $this->getData(self::CONTACT_PERSON);
    }

    /**
    * Set GroupId.
    */
    public function setContactPerson($group_id)
    {
        return $this->setData(self::CONTACT_PERSON, $group_id);
    }
   /**
    * Get EntityId.
    *
    * @return int
    */
    public function getContactNumber()
    {
        return $this->getData(self::CONTACT_NUMBER);
    }

   /**
    * Set EntityId.
    */
    public function setContactNumber($entityId)
    {
        return $this->setData(self::CONTACT_NUMBER, $entityId);
    }

    /**
    * Get EmployeeName.
    *
    * @return var
    */
    public function getBranchAddress()
    {
        return $this->getData(self::BRANCH_ADDRESS);
    }

    /**
    * Set EmpName.
    */
    public function setBranchAddress($name)
    {
        return $this->setData(self::BRANCH_ADDRESS, $name);
    }

    /**
    * Get Purchase Cap Limit.
    *
    * @return var
    */
    public function getBranchProvince()
    {
        return $this->getData(self::BRANCH_PROVINCE);
    }

    /**
    * Set Purchase Cap Limit.
    */
    public function setBranchProvince($purchase_cap_limit)
    {
        return $this->setData(self::BRANCH_PROVINCE, $purchase_cap_limit);
    }

    /**
    * Get PurchaseCapId.
    *
    * @return var
    */
    public function getBranchCity()
    {
        return $this->getData(self::BRANCH_CITY);
    }

    /**
    * Set PurchaseCapId.
    */
    public function setBranchCity($purchase_cap_id)
    {
        return $this->setData(self::BRANCH_CITY, $purchase_cap_id);
    }

    /**
    * Get Cosumed.
    *
    * @return var
    */
    public function getBranchPostcode()
    {
        return $this->getData(self::BRANCH_POSTCODE);
    }

    /**
    * Set Cosumed.
    */
    public function setBranchPostcode($consumed)
    {
        return $this->setData(self::BRANCH_POSTCODE, $consumed);
    }

    /**
    * Get Available.
    *
    * @return var
    */
    public function getShippingAddress()
    {
        return $this->getData(self::SHIPPING_ADDRESS);
    }

    /**
    * Set Available.
    */
    public function setShippingAddress($available)
    {
        return $this->setData(self::SHIPPING_ADDRESS, $available);
    }

    /**
    * Get Extension.
    *
    * @return var
    */
    public function getBillingAddress()
    {
        return $this->getData(self::BILLING_ADDRESS);
    }

    /**
    * Set Extension.
    */
    public function setBillingAddress($extension)
    {
        return $this->setData(self::BILLING_ADDRESS, $extension);
    }
   /**
    * Get UpdateTime.
    *
    * @return varchar
    */
    public function getUpdateAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

   /**
    * Set UpdateTime.
    */
    public function setUpdateTime($updateTime)
    {
        return $this->setData(self::UPDATED_AT, $updateTime);
    }

   /**
    * Get CreatedAt.
    *
    * @return varchar
    */
    public function getCreatedTime()
    {
        return $this->getData(self::CREATED_AT);
    }

   /**
    * Set CreatedAt.
    */
    public function setCreatedTime($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
    * Get UploadedBy.
    *
    * @return var
    */
    public function getShipCode()
    {
        return $this->getData(self::SHIP_CODE);
    }

    /**
    * Set UploadedBy.
    */
    public function setShipCode($uploader)
    {
        return $this->setData(self::SHIP_CODE, $uploader);
    }

     public function processData()
    {   
    
        
        $csv  = $this->getData('csv');
        $head = $this->getData('head');
        $data = NULL;

        //*** Convert header from space to _ ***///

        foreach ($head as $key => $value):
        
              if(strtolower($value) == 'limit'):          
                $value = 'pcap_limit';          
              endif;
              
              $key          = strtolower(str_replace(' ', '_', $key));        
              $head[$key]   = strtolower(str_replace(' ', '_', $value));              
              
              if($head[$key] == 'updated_date'):        
                $data .= $head[$key].' TIMESTAMP ';
              else:       
                $data .= $head[$key]." VARCHAR(255), ";       
              endif;
              
              $fieldName[] = $head[$key];
              
        endforeach;         
            
        $csvResult       = array_map("array_combine", array_fill(0, count($csv), $head), $csv);
        

        //**** Create Table using Model ***///
        
        // $data                    = $data. "id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY ";     
        // $tblData                 = array();      
        // $tblData['tablename']    = self::TABLE_NAME_VALUE;
        // $tblData['head']         = $data;        
        // $DataResponse            = Mage::getModel('admincontroller/tablecreate')->addData($tblData)->CreateTable(); 
        
        //**** Create Table using Model - End ***///    
        
        
        //**** Save file in Temp. Table ***///      
        
        $fieldName       = implode(",", $fieldName);        

        $saveTempProduct = $this->_saveTemp($csvResult, $fieldName);        
        
        //**** Save file in Temp. Table - End ***///    

        return $saveTempProduct;
    
    }
    
    
    protected function _saveTemp($csvResult, $fieldName)
    {
    

        $count          = 0;
        $countSave      = 0;
        $countBreak     = 10;
        $alreadysave    = 0;
        $getData        = array();
        $resData        = array();
        $tablename      = self::TABLE_NAME_VALUE;   
        $lastIncrement  = null;
        $dataSave = false;
        
        $this->_coreSession->unsRecords();
        $records        = count($csvResult);
    
        $filecount = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'companyaddresscount';
        $SaveCount = file_get_contents($filecount); 
  
        if(empty($SaveCount)):
            $SaveCount = 0;
        endif;
        
        
        foreach($csvResult as $_key=>$_value):
        
            $fieldValue                     = null;
            $getData                        = null;         
            $getData['companyid']           = null;         
            $getData['address']             = null;
            $getData['city']                = 0;
            $getData['province']            = 0;
            $getData['zipcode']             = 0;
            $getData['shiptoaddress']       = 0;
            $getData['contactno']           = null;         
            $getData['contactname']         = null;

            // var_dump($count);
            // var_dump($SaveCount);
          
            // if($count >= $SaveCount):
                // echo "<pre>";
                // var_dump($_value);
                // echo "</pre>";
                foreach($_value as $key=>$value):
                    //echo $key .'=>'. $value . '||';
                    if ($key == 'companyid'):

                        $getData['companyid'] = $value;
                    
                        $keyCode = $getData['companyid'];
                    
                    elseif ($key == 'address'):
                    
                    $getData['address'] = $value;
                    
                    elseif ($key == 'city'):
                        
                        $getData['city'] = $value;
                    
                    elseif ($key == 'province'):
                        
                        $getData['province'] = $value;
                        
                    elseif ($key == 'zipcode'):
                    
                        $getData['zipcode'] = $value;
                        
                    elseif ($key == 'shiptoaddress'):
                    
                        if(strtolower($value) == 'yes'):
                        
                            $getData['shiptoaddress'] = 1;
                            
                        else:
                        
                            $getData['shiptoaddress'] = 0;
                        
                        endif;
                        
                    elseif ($key == 'contactname'):
                    
                        $getData['contactname'] = $value;
                        
                    elseif ($key == 'contactno'):
                    
                        $getData['contactno'] = $value;
                        
                    endif;

                    $fieldValue[] = "'".$value."'"; 
                    
                endforeach;
                        
                $toValidate = array();
                
                $toValidate['address'] = $getData['address'];
                $toValidate['keyCode'] = $keyCode;
              
                if(!empty($keyCode)):
                
                    if($this->_isChecker($toValidate) == false):
                                        
                        
                        // $fieldValue = implode(",", $fieldValue); 
                        // $sql         = "INSERT INTO $tablename ($fieldName) VALUES ($fieldValue)";   
                        // $this->_resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->Query($sql);
                        // var_dump($getData);
                        $this->_saveData($getData);
                        
                        $count++;
                        
                        $resData[] = $keyCode .' - '.$getData['address'].' - <span style="color:green;">Success!</span>';
                        $this->_coreSession->setStatussave(1);
                        $this->_logger->debug($keyCode.' - '. $getData['address']);
                        
                        
                    else:
                    
                        $resData[] = $keyCode .' - '.$getData['address'].' - <span style="color:red;">Exist!</span>';
                        $this->_coreSession->setStatussave(0);
                        $this->_logger->debug($keyCode.' - '. $getData['address']);
                        
                    endif;
                    
                    $dataSave = true;
                    $countSave++;   
                    $this->_coreSession->setRecordsave($resData);                    
                
                endif;
                
            // endif;
            
            $count++;                   
            $remainingRec               = array();
            $remainingRec['Allrecords'] = $records;
            $remainingRec['Savecount']  = $count;       
            
            $this->_coreSession->setRecords($remainingRec);                          

            if($dataSave == true && $countSave == $countBreak):
                $countSave = 0;
                break;
            endif;                  
            
        endforeach; 
        
        return $resData;

    }
    
    protected function _gettaxclass($tax_type)
    {   
        $unilabTypeSql      = "SELECT class_id FROM tax_class WHERE class_name LIKE '$tax_type'";                       
        $unilabResult   = $this->_resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->fetchRow($unilabTypeSql);    
        
        return $unilabResult['class_id'];       
    }   
    

    
    protected function _isChecker($toValidate)
    {
    
        $branch_address     = $toValidate['address'];
        $keyCode            = $toValidate['keyCode'];
    
        $companyID          = $this->_isgetcompanyID($keyCode);         
        $tablename          = self::TABLE_NAME_VALUE;   
        $sql                = "SELECT * FROM rra_company_branches WHERE company_id='$companyID' AND branch_address LIKE '%$branch_address%'";                       
        $AccIdresult        = $this->_resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->fetchAll($sql);                  
        $total_rows         = count($AccIdresult);  

        if($total_rows == 0):
            $response = false;
        else:
            $response = true;       
        endif;
        
        return $response;
        
    }
    
    protected function _isgetcompanyID($keyCode)
    {
    
        $sql                = "SELECT customer_group_id FROM customer_group WHERE company_code='$keyCode'";                     
        $AccIdresult        = $this->_resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->fetchRow($sql);                  

        return $AccIdresult['customer_group_id'];
        
    }   
    
    protected function _isgetprovinceID($keyCode)
    {
    
        $sql                = "SELECT region_id FROM directory_country_region_name WHERE name LIKE '%$keyCode%'";                       
        $AccIdresult        = $this->_resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->fetchRow($sql);                  

        return $AccIdresult['region_id'];
        
    }       
    
    
    protected function _saveData($getData)
    {
    
    
        $companyID = $this->_isgetcompanyID($getData['companyid']);
        $provinceID = $this->_isgetprovinceID($getData['province']);
        // var_dump($getData);
        try {
            
            if($getData['shiptoaddress'] == false):
            
                $getData['billtoaddress']  = 1;
                
            else:
            
                $getData['billtoaddress']  = 0;
            
            endif;
        
            $this->_resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->beginTransaction();    
        
            //*** Insert data to rra_company_branches Company Address
            $fields                         = array();
            $fields['company_id']           = $companyID;
            $fields['branch_address']       = $getData['address'];
            $fields['branch_province']      = $provinceID;
            $fields['branch_city']          = $getData['city'];
            $fields['branch_postcode']      = $getData['zipcode'];
            $fields['shipping_address']     = $getData['shiptoaddress'];
            $fields['billing_address']      = $getData['billtoaddress'];
            $fields['contact_number']       = $getData['contactno'];
            $fields['contact_person']       = $getData['contactname'];
            $fields['created_time']         = date('Y-m-d H:i:s');
            $fields['update_time']          = date('Y-m-d H:i:s');

            //var_dump($fields);   
            $this->_resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->insert('rra_company_branches', $fields);
            $this->_resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->commit();              
        
            
            }catch(Exception $e){
            
                $this->_logger->log(\Psr\Log\LogLevel::DEBUG, $e->getMessage());
            }   
                
                    
    }
}