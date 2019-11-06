<?php
/**
 * @category  Unilab
 * @package   Unilab_Benefits->Productpricelist
 * @author    Kristian Claridad
 */
namespace Unilab\Benefits\Controller\Adminhtml\Productpricelist;

class Importresult extends \Magento\Backend\App\Action
{
    const TABLE_NAME_VALUE      = 'rra_pricelistproduct';
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
        $filecsv = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'productpricelist';
        $csv = file_get_contents($filecsv);
        $csv = json_decode($csv);
        $filehead = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'productpricelisthead';
        $head = file_get_contents($filehead);
        $head = json_decode($head);
        $filecount = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'productpricelistcount';

        // $csv    = array_map("array_combine", array_fill(0, count($csv), $head), $csv);
        
        $fieldName = [];
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

        $records  	= $this->_coreSession->getRecords();	
		$status  	= $this->_coreSession->getStatussave();
		$this->_coreSession->setsavecount($records['Savecount']); 
        file_put_contents($filecount,$records['Savecount']);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Import Product Price List Result'));
        return $resultPage;
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
		$filecount = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'productpricelistcount';
        $SaveCount = file_get_contents($filecount);
        
        if(empty($SaveCount)):
			$SaveCount = 0;
        endif;
		
		foreach($csvResult as $_key=>$_value):
		
			$fieldValue 						= null;
			$getData  							= null;
			$getData['pricelist_id']			= null;
			$getData['product_sku']				= null;
			$getData['product_name']			= null;
			$getData['qty_from']				= 0;
			$getData['qty_to']					= 0;
			$getData['unit_price']				= 0;
			$getData['discount_in_amount']		= 0;
			$getData['discount_in_percent'] 	= 0;
            $getData['uploaded_by']				= null;
            $from_date = null;
            $to_date = null;

			if($count >= $SaveCount):
				
				foreach($_value as $key=>$value):
					if ($key == 'pricelist_id'):
						$getData['pricelist_id'] = $value;
						$pricelist_id = $value;
					elseif ($key == 'product_sku'):
						$getData['product_sku'] = $value;
						$keyCode = $getData['product_sku'];				
					elseif ($key == 'product_name'):
						$getData['product_name'] = str_replace("'", " ", $value);
						$value = str_replace("'", " ", $value);
					elseif ($key == 'qty_from'):
						$getData['qty_from'] = $value;
					elseif ($key == 'qty_to'):
						$getData['qty_to'] = $value;
					elseif ($key == 'unit_price'):
						$getData['unit_price'] = $value;
					elseif ($key == 'discount_in_amount'):
						$getData['discount_in_amount'] = $value;
					elseif ($key == 'discount_in_percent'):
						$getData['discount_in_percent'] = $value;
					elseif ($key == 'from_date'):
						$getData['from_date'] = $value;
						$value 		= date('Y-m-d',strtotime($value));
						$from_date 	= date('Y-m-d',strtotime($value));
					elseif ($key == "to_date"):
						$getData['to_date'] = $value;
						$value 		= date('Y-m-d',strtotime($value));
						$to_date 	= date('Y-m-d',strtotime($value));
					endif;
					$fieldValue[] = "'".$value."'";	
				endforeach;
				$fieldValue[3] = str_replace(",",'',$fieldValue[3]);
				$fieldValue[4] = str_replace(",",'',$fieldValue[4]);
				$fieldValue[5] = str_replace(",",'',$fieldValue[5]);
				$fieldValue[6] = str_replace(",",'',$fieldValue[6]);
				$fieldValue[7] = str_replace(",",'',$fieldValue[7]);
			
				if(!empty($keyCode) && !empty($pricelist_id)):
					$dataSave = true;
					$countSave++;
					$currentnumber = $count + 1;
					if ($count >= $SaveCount):
								
						if($this->_isChecker($keyCode, $pricelist_id, $from_date, $to_date) == false):
							$fieldValue = implode(",", $fieldValue);
							$sql 		= "INSERT INTO $tablename ($fieldName) VALUES ($fieldValue)";		
							$this->_getConnection()->Query($sql);
							$resData[] = $currentnumber. '. '. $getData['pricelist_id']  .' : '. $keyCode .' - '.$getData['product_name'].' - <span style="color:green;">Success!</span>';
							$this->_coreSession->setStatussave(1);
						else:
							$resData[] = $currentnumber. '. '. $getData['pricelist_id']  .' : '. $keyCode .' - '.$getData['product_name'].' - <span style="color:red;">Exist!</span>';
							$this->_coreSession->setStatussave(0);
						endif;
					endif;
						
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
    protected function _getConnection()
    {
		$connection = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        return $connection;
    }

    protected function _isChecker($keyCode, $pricelist_id, 	$from_date, $to_date)
	{
		$tablename 		= self::TABLE_NAME_VALUE;
		// $sql 			= "SELECT * FROM $tablename WHERE product_sku LIKE '$keyCode' AND  pricelist_id='$pricelist_id' AND  from_date='$from_date' AND  to_date='$to_date'";						
		$sql 			= "SELECT pricelist_id FROM $tablename WHERE product_sku LIKE '$keyCode' AND  pricelist_id='$pricelist_id'";						
		$AccIdresult 	= $this->_getConnection()->fetchAll($sql);					
		$total_rows 	= count($AccIdresult);	
		if($total_rows == 0):
			$response = false;
		else:
			$response = true;		
		endif;
		return $response;
	}
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
