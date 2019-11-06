<?php

namespace Unilab\Benefits\Model;


class CompanyBranchHandler extends \Magento\Payment\Model\Method\AbstractMethod {
    
    const CACHE_TAG = 'rra_company_branches';
    const TABLE_NAME_VALUE = 'rra_company_branches';
   
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
		\Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\App\Filesystem\DirectoryList $directorylist,
        \Psr\Log\LoggerInterface $logger,
        $resourcePrefix = null
    ) {
        // parent::__construct($context, $resourcePrefix);
		$this->_resourceConnection = $resourceConnection;
		$this->_coreSession = $coreSession;
		$this->_directorylist = $directorylist;
		$this->_logger = $logger;
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

            if($count >= $SaveCount):

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
                    if($this->_isgetcompanyID($toValidate['keyCode'])):
                        if(empty($getData['address']) || empty($getData['city']) || empty($getData['province'])){
                          
                            $resData[] = $keyCode .' - Empty <span style="color:red;">[address|city|province]'.'</span>';
                            $this->_coreSession->setStatussave(0);
                        }else if($this->_isChecker($toValidate) == false):
                            $this->_saveData($getData);
                            $count++;
                            $resData[] = $keyCode .' - '.$getData['address'].' - <span style="color:green;">Success!</span>';
                            $this->_coreSession->setStatussave(1);
                            $this->_logger->debug($keyCode.' - '. $getData['address']);
                        else:
                            $resData[] = $keyCode .' - '.$getData['address'].' - <span style="color:red;">Already Exist!</span>';
                            $this->_coreSession->setStatussave(0);
                            $this->_logger->debug($keyCode.' - '. $getData['address']);
                        endif;
                    else:
                        $resData[] = $keyCode .' - '.$getData['address'].' - <span style="color:red;">Company Not Exist!</span>';
                        $this->_coreSession->setStatussave(0);
                        $this->_logger->debug($keyCode.' - '. $getData['address']);
                    endif;
                    $dataSave = true;
                    $countSave++;   
                    $this->_coreSession->setRecordsave($resData);                    
                endif;
            endif;
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
        $sql                = "SELECT id FROM rra_company_branches WHERE company_id='$companyID' AND branch_address LIKE '%$branch_address%'";                       
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