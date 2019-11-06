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
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
    ) {
        parent::__construct($context);
        $this->gridFactory = $gridFactory;
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
        $connection->beginTransaction();


        $fullpath = $_FILES['csv_file']['tmp_name'];
        $filename = $_FILES['csv_file']['name'];
        $size = $_FILES['csv_file']['size'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        //**Get User Session  
        $user                   = $this->userSession->getUser(); 
        $userId                 = $user->getUserId();
        $userUsername           = $user->getUsername();
  
        /**
        *   Validate csv file
        */

        $csv    = array_map("str_getcsv", file($fullpath));
        $head   = array_shift($csv);
        $csv    = array_map("array_combine", array_fill(0, count($csv), $head), $csv);
        
        //Count if column is complete
        $format_csv = 0;
        
        foreach($head as $_dataVal):
            if(strtolower($_dataVal) == 'emp_id'):
                $format_csv++;
            elseif(strtolower($_dataVal) == 'purchase_cap_name'):
                $format_csv++;
            elseif(strtolower($_dataVal) == 'purchase_cap_limit'):
                $format_csv++;
            elseif(strtolower($_dataVal) == 'consumed'):
                $format_csv++;
            elseif(strtolower($_dataVal) == 'extension'):
                $format_csv++;
            elseif(strtolower($_dataVal) == 'refresh_period'):
                $format_csv++;
            elseif(strtolower($_dataVal) == 'start_date'):
                $format_csv++;
            elseif(strtolower($_dataVal) == 'refresh_date'):
                $format_csv++;
            elseif(strtolower($_dataVal) == 'available'):
                $format_csv++;
            elseif(strtolower($_dataVal) == 'created_time'):
                $format_csv++;
            elseif(strtolower($_dataVal) == 'update_time'):
                $format_csv++;
            endif;
        endforeach;

        if (strtolower($ext) == 'csv' && $format_csv < 7):
            
            $this->messageManager->addErrorMessage('Incorrect content format.');
            $this->_redirect('unilab_benefits/employeebenefitscontroller/import');
            
            return;
        elseif(strtolower($ext) == 'csv'):      
            
            $purchase_cap_name      = null;
            $purchase_cap_limit     = null;
            $emp_id                 = null;
            $available              = 0;
            $extension              = 0;
            $consumed               = 0;
            $created_time           = null;
            $update_time            = null;
            
            $duplicateData          = null;         
            $error                  = false;        
            $errormsg               = false;                
            $count                  = 0; 
            
            foreach($csv as $_data):
            
                $emp_id             = null;
                $purchase_cap_name  = null;
                $purchase_cap_limit = null;
                $available          = 0;
                $extension          = 0;
                $consumed           = 0;
                $date_of_birth      = null;
                $created_time       = null;
                $update_time        = null;
                
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
                

                    //Search entity_id from customer_entity_varchar
                    
                    $selectEntity = $connection->select()->from('customer_entity_varchar', array('*'))->where('value=?',$emp_id); 
                    
                    $rowEntity = $connection->fetchRow($selectEntity);
                    
                    if(!empty($rowEntity['entity_id'])):
                    
                        $customerData  = $this->_objectManager->create("\Magento\Customer\Model\Customer")->load($rowEntity['entity_id']);
                        
                    else:
                    
                        $erroID   = $emp_id;
                        $errormsg = true;
                        
                        break;
                        
                    endif;
                    
                    
                    $sql = "SELECT id FROM rra_emp_purchasecap WHERE purchase_cap_id LIKE '%$purchase_cap_name%'";  
                    
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
                        
                        if($count > 0):
                        
                            $duplicateData .= ', '.$emp_id .'['.$purchase_cap_name.']';
                            
                        else:
                        
                            $duplicateData .= $emp_id.'['.$purchase_cap_name.']';
                            
                        endif;
                        
                        $count++;

                    endif;
                    

                    
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
                    
                    // echo "erro_logs";
                    // echo "<pre>";
                    // var_dump($fields);
                    // echo "</pre>";

                    $connection->insert('rra_error_logs', $fields);
                    
                    // $connection->commit();  
                        
                    
                endif;

                
            endforeach; 
            
            if($errormsg == true):
            
                $this->messageManager->addErrorMessage("Employee ID $erroID not exist in database.");
                
                $this->_redirect('unilab_benefits/employeebenefitscontroller/import');
            
            elseif ($error == true):
            
                $this->messageManager->addErrorMessage("Employee ID: $duplicateData with Purchase cap name is/are already exist.");
                
                // $this->loadLayout();
                
                // $this->_setActiveMenu('customer/manage');
               
                // $this->_addBreadcrumb(__('Manage Export CSV File'), __('Manage Export CSV File'));
                
                // $this->_addBreadcrumb(__('Manage Export CSV File'), __('Manage Export CSV File'));
               
                // $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
               
                // $this->_addContent($this->getLayout()->createBlock('benefits/employeebenefitscontroller_continue'))
                
                //      ->_addLeft($this->getLayout()->createBlock('benefits/employeebenefitscontroller_import_continue'));
                   
                // $this->renderLayout();      


            else:

            
                foreach($csv as $_data):
                
                    $emp_id             = null;
                    $purchase_cap_name  = null;
                    $purchase_cap_limit = null;
                    $available          = 0;
                    $extension          = 0;
                    $consumed           = 0;
                    $date_of_birth      = null;
                    $created_time       = null;
                    $update_time        = null;
                    
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
                                $refresh_period     = $this->_getresfreshPeriod($_value);                           
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
                    

                        //Search entity_id from customer_entity_varchar
                        
                        $selectEntity   =   $connection->select()->from('customer_entity_varchar', array('*'))->where('value=?',$emp_id); 
                        
                        $rowEntity      =   $connection->fetchRow($selectEntity);
                        
                        $customerData   = $this->_objectManager->create('\Magento\Customer\Model\Customer')->load($rowEntity['entity_id']);
                        
                        
                        $sql = "SELECT id FROM rra_emp_purchasecap WHERE purchase_cap_id LIKE '%$purchase_cap_name%'";  
                        
                        $connection2 = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);

                        foreach ($connection2->fetchAll($sql) as $get_id) {
                        
                            $purchase_cap_id =  $get_id['id'];
                            
                        }                   
                        
                        
                        if(empty($purchase_cap_id)):
                        
                            $purchase_cap_id = 0;
                            
                        endif;
                        
                        $benefitcheck = $this->_isbenefitcheck($purchase_cap_id, $emp_id);
                        
                        if($benefitcheck == false):
                        
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
                            
                            // echo "<pre>";
                            // var_dump($fields);
                            // echo "</pre>";
                            $connection->insert('rra_emp_benefits', $fields);
                            
                            //$connection->commit();  
                            
                        endif;
                        
                    endif;

                    
                endforeach;             
            
            
                $this->messageManager->addSuccessMessage('All Data was successfully imported.');
                
                $this->_redirect('unilab_benefits/employeebenefitscontroller/import');
                
            endif;

    
        else:       
        
            $this->messageManager->addErrorMessage($filename.' - is not a CSV format.');
            
            $this->_redirect('unilab_benefits/employeebenefitscontroller/import');
            
            return;
        
        endif;
        $connection->commit(); 
    }
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
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
}
