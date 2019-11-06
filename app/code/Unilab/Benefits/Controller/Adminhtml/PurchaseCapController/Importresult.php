<?php
/**
 * @category  Unilab
 * @package   Unilab_Benefits
 * @author    Kristian Claridad
 */
namespace Unilab\Benefits\Controller\Adminhtml\PurchaseCapController;

class Importresult extends \Magento\Backend\App\Action
{
    const TABLE_NAME_VALUE      = 'wspi_purchasecap';
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
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\App\Filesystem\DirectoryList $directorylist
    ) {
        $this->resourceConnection = $resourceConnection;
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
        if(!defined('DS')){
            define('DS',DIRECTORY_SEPARATOR);
        }
        $filecsv = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'purchasecap';
        $csv = file_get_contents($filecsv);
        $csv = json_decode($csv);
        $filehead = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'purchasecaphead';
        $head = file_get_contents($filehead);
        $head = json_decode($head);
        $filecount = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'purchasecapcount';

        $dataArr 			= array();		
		$dataArr['csv'] 	= $csv;
		$dataArr['head'] 	= $head;
        
        $SaveData = $this->_processData($dataArr);

        $records  	= $this->_coreSession->getRecords();	
		$status  	= $this->_coreSession->getStatussave();
		$this->_coreSession->setsavecount($records['Savecount']); 
        file_put_contents($filecount,$records['Savecount']);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Import Purchase Cap Limit Result'));
        
        return $resultPage;
    }
    protected function _getConnection()
    {
		$connection = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
		
        return $connection;
    }

    public function _processData($dataArr)
    {	
		$csv  = $dataArr['csv'];
		$head = $dataArr['head'];
        $data = "";
		//*** Convert header from space to _ ***///
		foreach ($head as $key => $value):
			  if(strtolower($value) == 'limit'):		  
				$value = 'pcap_limit';			
			  endif;
			  
			  $key 			= strtolower(str_replace(' ', '_', $key));		  
			  $head[$key] 	= strtolower(str_replace(' ', '_', $value));			  
			  
			  $fieldName[] = $head[$key];
		endforeach;	
			
		$csvResult 		 = array_map("array_combine", array_fill(0, count($csv), $head), $csv);
		
		//**** Save file in Temp. Table ***///		
		$fieldName 		 = implode(",", $fieldName);		
		$saveTempProduct = $this->_saveTemp($csvResult, $fieldName);		
		//**** Save file in Temp. Table - End ***///	
		return $saveTempProduct;
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

		$records		= count($csvResult);
        $dataSave = null;
		$filecount = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'purchasecapcount';
        $SaveCount = file_get_contents($filecount);
        
        if(empty($SaveCount)):
			$SaveCount = 0;
        endif;

		foreach($csvResult as $_key=>$_value):
		
			$fieldValue 					= null;
			$getData  						= null;
			$getData['pcapid']				= null;
			$getData['pcapname']			= null;
			$getData['pcaplimit']			= 0;
			$getData['extlimit']			= 0;
			$getData['refreshperiod']		= 0;
			$getData['refreshstartdate']	= 0;
			$getData['refreshresetdate']	= 0;		
			$getData['transactiontype'] 	= 0;
			$getData['tendertype'] 			= 0;

            if($count >= $SaveCount):
                foreach($_value as $key=>$value):
                
                    if ($key == 'pcapid'):
                        $getData['pcapid'] = $value;
                        $keyCode = $getData['pcapid'];
                    elseif ($key == 'pcapname'):
                        $getData['pcapname'] = $value;
                    elseif ($key == 'transactiontype'):
                        $getData['transactiontype'] = $value;
                    endif;
                    
                    $fieldValue[] = "'".$value."'";	
                    
                endforeach;
                        
                if(!empty($keyCode)):
                    $currentnumber = $count + 1;
                    if($this->_isChecker($keyCode) == false):
                        $this->_saveData($getData);
                        
                        $resData[] = $currentnumber. '. '.$keyCode .' - '.$getData['pcapname'].' - <span style="color:green;">Success!</span>';
                        $this->_coreSession->setStatussave(1);
                    else:
                        $resData[] = $currentnumber. '. '.$keyCode .' - '.$getData['pcapname'].' - <span style="color:red;">Already Exist!</span>';
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
        
		return $this;
	}
	
	protected function _gettaxclass($tax_type)
	{	
		$unilabTypeSql 		= "SELECT class_id FROM tax_class WHERE class_name LIKE '$tax_type'";
		$unilabResult 	= $this->_getConnection()->fetchRow($unilabTypeSql);	
		return $unilabResult['class_id'];		
	}	
	

	
	protected function _isChecker($keyCode)
	{
		$sql 				= "SELECT * FROM rra_emp_purchasecap WHERE purchase_cap_id LIKE '$keyCode'";
		$AccIdresult 		= $this->_getConnection()->fetchAll($sql);					
		$total_rows 		= count($AccIdresult);	

		if($total_rows == 0):
			$response = false;
		else:
			$response = true;		
		endif;
		return $response;
	}
	
	protected function _isgetcompanyID($keyCode)
	{
		$sql 				= "SELECT customer_group_id FROM customer_group WHERE company_code='$keyCode'";
		$AccIdresult 		= $this->_getConnection()->fetchRow($sql);					
		return $AccIdresult['customer_group_id'];
	}	
	
	protected function _getrefreshperiod($keyCode)
	{	
		$unilabTypeSql 		= "SELECT option_id FROM eav_attribute_option_value WHERE value LIKE '$keyCode'";
		$unilabResult 	= $this->_getConnection()->fetchRow($unilabTypeSql);	
		return $unilabResult['option_id'];		
	}	
	
	protected function _isgetTransactionID($keyCode)
	{
		$sql 				= "SELECT id FROM rra_transaction_type WHERE transaction_name LIKE '%$keyCode%'";
		$AccIdresult 		= $this->_getConnection()->fetchRow($sql);					
		return $AccIdresult['id'];
	}		
	
	
	protected function _saveData($getData)
	{
		$transactionID 		= $this->_isgetTransactionID($getData['transactiontype']);
		try {
			$this->_getConnection()->beginTransaction();	
			$refreshstartdate = strtotime($getData['refreshstartdate']);
			$refreshresetdate = strtotime($getData['refreshresetdate']);
			
			//*** Insert data to rra_emp_purchasecap Purchase Cap
			$fields 						= array();
			$fields['purchase_cap_id']		= $getData['pcapid'];
			$fields['purchase_cap_des']		= $getData['pcapname'];
			//$fields['purchase_cap_limit']	= $getData['pcaplimit'];
			//$fields['extension_amount']		= $getData['extlimit'];
			$fields['tnx_id']				= $transactionID;
			//$fields['start_date']			= date('Y-m-d',$refreshstartdate);
			//$fields['end_date']				= date('Y-m-d',$refreshresetdate);
			$fields['created_time']			= date("Y-m-d H:i:s");
			$fields['update_time']			= date("Y-m-d H:i:s");
			$this->_getConnection()->insert('rra_emp_purchasecap', $fields);
			$this->_getConnection()->commit();				
			
			}catch(\Exception $e){
			    $this->messageManager->addError(__($e->getMessage()));
			}	
				
	}
    protected function _isAllowed()
    {
        return true;
    }
}
