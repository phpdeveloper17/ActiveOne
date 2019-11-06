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

class Importresult extends \Magento\Backend\App\Action
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
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Unilab\Benefits\Model\EmployeeBenefitFactory $gridFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Backend\Model\Auth\Session $userSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\App\Filesystem\DirectoryList $directorylist
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
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
        $filecsv = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'empbenefit';
        $csv = file_get_contents($filecsv);
        $csv = json_decode($csv);
        $filehead = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'empbenefithead';
        $head = file_get_contents($filehead);
        $head = json_decode($head);
        $filecount = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'empbenefitcount';

        $csv    = array_map("array_combine", array_fill(0, count($csv), $head), $csv);
        
        $saveTempProduct = $this->_saveTemp($csv);

        $records  	= $this->_coreSession->getRecords();	
		$status  	= $this->_coreSession->getStatussave();
		$this->_coreSession->setsavecount($records['Savecount']); 
        file_put_contents($filecount,$records['Savecount']);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Import Employee Benefits Result'));
        return $resultPage;
    }
    protected function _saveTemp($csv){
        $visitorData = $this->remoteAddress->getRemoteAddress(true);
        $connection = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $user                   = $this->userSession->getUser(); 
        $userId                 = $user->getUserId();
        $userUsername           = $user->getUsername();
        // $connection->beginTransaction();
        
        
        $purchase_cap_name      = null;
        $purchase_cap_limit     = 0;
        $emp_id                 = 0;
        $available              = 0;
        $extension              = 0;
        $consumed               = 0;
        $created_time           = null;
        $update_time            = null;
        
        $duplicateData          = null;
        $error                  = false;
        $errormsg               = false;
        
        $count 			= 0;
		$countSave		= 0;
		$countBreak 	= 10;
		$alreadysave	= 0;
		$getData 		= array();
        $resData		= array();

        $records		= count($csv);
        $dataSave = null;
		$filecount = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'empbenefitcount';
        $SaveCount = file_get_contents($filecount);
        
        if(empty($SaveCount)):
			$SaveCount = 0;
        endif;
        foreach($csv as $_data):
            
            $emp_id             = null;
            $purchase_cap_name  = null;
            $purchase_cap_limit = 0;
            $available          = 0;
            $extension          = 0;
            $consumed           = 0;
            $date_of_birth      = null;
            $created_time       = null;
            $update_time        = null;
            
            if($count >= $SaveCount):
                foreach($_data as $_key=>$_value):
                    if(!empty($_value)):
                        if (strtolower($_key) == 'purchase_cap_name'):
                            $purchase_cap_name  = $_value;
                        elseif (strtolower($_key) == 'emp_id'):
                            $emp_id = $_value;
                        elseif (strtolower($_key) == 'purchase_cap_limit'):
                            $purchase_cap_limit = $_value;
                        elseif (strtolower($_key) == 'created_time'):
                            $created_time   = $_value;
                        elseif (strtolower($_key) == 'date_of_birth'):
                            $date_of_birth  = $_value;
                        elseif (strtolower($_key) == 'consumed'):
                            $consumed   = $_value;
                        elseif (strtolower($_key) == 'extension'):
                            $extension  = $_value;
                        elseif (strtolower($_key) == 'available'):
                            $available  = $_value;                          
                        elseif (strtolower($_key) == 'refresh_period'):
                            $refresh_period = $_value;
                        elseif (strtolower($_key) == 'start_date'):
                            $start_date = $_value;                          
                        elseif (strtolower($_key) == 'refresh_date'):
                            $refresh_date   = $_value;
                        elseif (strtolower($_key) == 'update_time'):
                            $update_time    = $_value;                      
                        endif;
                    endif;
                endforeach;
               
                if (!empty($emp_id)):
                    $currentnumber = $count + 1;
                    //Search entity_id from customer_entity_varchar
                    $selectEntity = $connection->select()->from('customer_entity_varchar', array('*'))->where('value=?',$emp_id); 
                    $rowEntity = $connection->fetchRow($selectEntity);
                    
                    if(!empty($rowEntity['entity_id'])):
                        $customerData  = $this->_objectManager->create("\Magento\Customer\Model\Customer")->load($rowEntity['entity_id']);
                    else:
                        $erroID   = $emp_id;
                        $errormsg = true;
                        // break;
                    endif;
                    
                    $sql = "SELECT id FROM rra_emp_purchasecap WHERE purchase_cap_des LIKE '%$purchase_cap_name%'";  
                    $connection2 = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
                    foreach ($connection2->fetchAll($sql) as $get_id) {
                        $purchase_cap_id =  $get_id['id'];
                    }                   
                    
                    if(empty($purchase_cap_id)):
                        $purchase_cap_id = 0;
                    endif;
                        
                    $selectcapExist =  $connection->select()->from('rra_emp_benefits', array('*'))
                    ->where('entity_id=?',$rowEntity['entity_id'])
                    ->where('purchase_cap_id=?',$purchase_cap_id); 
                    $rowcapexist = $connection->fetchRow($selectcapExist);
                    
                    if(count($rowcapexist) >= 2 ):
                        $error = true;
                        //*** Insert data to customer_entity
                        // if($count > 0):
                        //     $duplicateData .= ', '.$emp_id .'['.$purchase_cap_name.']';
                        // else:
                        //     $duplicateData .= $emp_id.'['.$purchase_cap_name.']';
                        // endif;
                        // $count++;
                    endif;
                    if($available != $purchase_cap_limit){
                        $available = $purchase_cap_limit;
                    }
                    if($errormsg == false):
                        $fields                         = array();
                        $fields['emp_id']               = $emp_id;
                        $fields['entity_id']            = $rowEntity['entity_id'];
                        $fields['emp_name']             = $customerData['firstname'] . ' ' . $customerData['lastname'];
                        $fields['purchase_cap_limit']   = $purchase_cap_limit;
                        $fields['purchase_cap_id']      = $purchase_cap_id;
                        $fields['consumed']             = $consumed;
                        $fields['available']            = $available;                                       
                        $fields['extension']            = $extension;
                        $fields['created_time']         = date("Y-m-d",strtotime($start_date));                 
                        $fields['update_time']          = date("Y-m-d",strtotime($refresh_date));
                        $fields['purchase_cap_id']      = $purchase_cap_id;
                        $fields['uploadedby']           = $userUsername;
                        $fields['date_uploaded']        = date('Y-m-d H:i:s');
                        $fields['uploaded_ip']          = $visitorData;                 

                        $connection->insert('rra_error_logs', $fields);                    
                    endif;
                    unset($fields);

                    $benefitcheck = $this->_isbenefitcheck($purchase_cap_id, $emp_id);
                    if($errormsg == true):
                        $resData[] = $currentnumber.' - Employee ID'.' <span style="color:red;"> '.$erroID.' </span>not exist in database!';
                        $this->_coreSession->setStatussave(0);
                    elseif($benefitcheck == false):
                        $refresh_period = $this->_getresfreshPeriod(strtolower($refresh_period));
                        $fields                         = array();
                        $fields['emp_id']               = $emp_id;
                        $fields['entity_id']            = $rowEntity['entity_id'];
                        $fields['emp_name']             = $customerData['firstname'] . ' ' . $customerData['lastname'];
                        $fields['purchase_cap_limit']   = $purchase_cap_limit;
                        $fields['purchase_cap_id']      = $purchase_cap_id;
                        $fields['consumed']             = $consumed;
                        $fields['available']            = $available;
                        $fields['extension']            = $extension;
                        $fields['refresh_period']       = $refresh_period;                          
                        $fields['start_date']           = date("Y-m-d",strtotime($start_date)); 
                        $fields['refresh_date']         = date("Y-m-d",strtotime($refresh_date));       
                        $fields['created_time']         = date('Y-m-d H:i:s');                  
                        $fields['update_time']          = date('Y-m-d H:i:s');
                        $fields['purchase_cap_id']      = $purchase_cap_id;
                        $fields['uploadedby']           = $userUsername;
                        $fields['date_uploaded']        = date('Y-m-d H:i:s');
                        
                        $connection->insert('rra_emp_benefits', $fields);
                        //$connection->commit();
                        $resData[] = $currentnumber.' - '.$emp_id .'['.$purchase_cap_name.']'.' - <span style="color:green;">Success!</span>';
                        $this->_coreSession->setStatussave(1);
                    else:
                        $resData[] = $currentnumber.' - '.$emp_id .'['.$purchase_cap_name.']'.' - <span style="color:red;">with Purchase cap name is/are already exist!</span>';
                        $this->_coreSession->setStatussave(0);
                    endif;
                    $dataSave = true;
                    $countSave++;	
                    $this->_coreSession->setRecordsave($resData);	
                endif;
            endif;
            $count++;					
            $remainingRec  				= array();
            $remainingRec['Allrecords']	= $records;
            $remainingRec['Savecount']	= $count;		
            
            $this->_coreSession->setRecords($remainingRec);							
            
            if($dataSave == true && $countSave == $countBreak):
                $countSave = 0;
                break;
            endif;
        endforeach;
    }
    protected function _getresfreshPeriod($refresh_period)
    {   
        $unilabTypeSql      = "SELECT option_id FROM eav_attribute_option_value WHERE value LIKE '$refresh_period'";
        $unilabResult   = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->fetchRow($unilabTypeSql);    
        return $unilabResult['option_id'];      
    }  

    protected function _isbenefitcheck($pcapid, $emp_idpre)
    {
        $unilabTypeSql      = "SELECT id FROM rra_emp_benefits WHERE purchase_cap_id LIKE '$pcapid' AND emp_id LIKE '$emp_idpre' ";                  
        $unilabResult   = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->fetchRow($unilabTypeSql);    
        return  count($unilabResult['id']);
        
    }
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
