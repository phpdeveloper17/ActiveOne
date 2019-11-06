<?php
/**
 * @category  Unilab
 * @package   Unilab_Benefits
 * @author    Kristian Claridad
 */
namespace Unilab\Benefits\Controller\Adminhtml\Pricelevel;

class Importresult extends \Magento\Backend\App\Action
{
    const TABLE_NAME_VALUE      = 'wspi_pricelevel';
    /**
     * @var \Magento\Framework\Registry
     */
    protected $resourceConnection;
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
        $filecsv = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'pricelevel';
        $csv = file_get_contents($filecsv);
        $csv = json_decode($csv);
        $filehead = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'pricelevelhead';
        $head = file_get_contents($filehead);
        $head = json_decode($head);
        $filecount = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'pricelevelcount';

        $csv    = array_map("array_combine", array_fill(0, count($csv), $head), $csv);
        
        //Count if column is complete
        foreach ($head as $key => $value):
            if(strtolower($value) == 'id'):
            $value = 'price_level_id';
            endif;
            $key 			= strtolower(str_replace(' ', '_', $key));
            $head[$key] 	= strtolower(str_replace(' ', '_', $value));
            $fieldName[] = $head[$key];
            
        endforeach;
        
        $csvResult 		 = array_map("array_combine", array_fill(0, count($csv), $head), $csv);
       
        $fieldName 		 = implode(",", $fieldName);		
        $saveTempProduct = $this->_saveTemp($csvResult, $fieldName);

        $records  	= $this->_coreSession->getRecords();	
		$status  	= $this->_coreSession->getStatussave();
		$this->_coreSession->setsavecount($records['Savecount']); 
        file_put_contents($filecount,$records['Savecount']);
        
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Import Price Level Result'));
        
        return $resultPage;
    }
    protected function _getConnection()
    {
		$connection = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
		
        return $connection;
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
        

        $records		= count($csvResult);
        $dataSave = null;
		$filecount = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'pricelevelcount';
        $SaveCount = file_get_contents($filecount);
        
        if(empty($SaveCount)):
			$SaveCount = 0;
        endif;
        

        foreach($csvResult as $_key=>$_value):

            $fieldValue 				= null;
			$getData  					= null;
			$getData['price_level_id']	= null;
			$getData['name']			= null;
            $getData['active']			= 0;
            
            
            if($count >= $SaveCount):
                foreach($_value as $key=>$value):
					if ($key == 'price_level_id'):
						$getData['price_level_id'] = $value;
						$keyCode = $getData['price_level_id'];
					elseif ($key == 'name'):
						$getData['name'] = $value;
					elseif ($key == 'active'):
						if(strtolower($value) == 'yes'):					
							$getData['active'] = 1;					
						endif;
					endif;

					$fieldValue[] = "'".$value."'";	
					
                endforeach;
                
                    if(!empty($keyCode)):
                    
                        if($this->_isChecker($keyCode) == false):
                            $this->_saveData($getData);
                            $resData[] = $keyCode .' - '.$getData['name'].' - <span style="color:green;">Success!</span>';
                            $this->_coreSession->setStatussave(1);
                        else:
                        
                            $resData[] = $keyCode .' - '.$getData['name'].' - <span style="color:red;">Already Exist!</span>';
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
    protected function _isChecker($keyCode)
	{
		$sql 				= "SELECT * FROM rra_pricelevelmaster WHERE price_level_id LIKE '$keyCode'";						
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
			$this->_getConnection()->beginTransaction();	
			
			
			$fields 					= array();
			$fields['price_level_id']	= $getData['price_level_id'];
			$fields['price_name']		= $getData['name'];
			$fields['is_active']		= $getData['active'];
			$fields['created_time']		= date("Y-m-d H:i:s");
			$fields['update_time']		= date("Y-m-d H:i:s");
			$this->_getConnection()->insert('rra_pricelevelmaster', $fields);
			$this->_getConnection()->commit();				
		
			}catch(Exception $e){
                $this->messageManager->addErrowMessage($e->getMessage());
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
