<?php
/**
 * @category  Unilab
 * @package   Unilab_Pricelist
 * @author    Kristian Claridad
 */
namespace Unilab\Pricelist\Controller\Adminhtml\Promo\Catalog;

class Importresult extends \Magento\Backend\App\Action
{
    const TABLE_NAME_VALUE      = 'wspi_pricelist';
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
        \Magento\Backend\Model\Auth\Session $userSession,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Unilab\Pricelist\Logger\Logger $loggerInteface,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\App\Filesystem\DirectoryList $directorylist
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->userSession = $userSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory; 
        $this->coreRegistry = $coreRegistry;
        $this->_logger = $loggerInteface;
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
        $filecsv = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'pricelist';
        $csv = file_get_contents($filecsv);
        $csv = json_decode($csv);
        $filehead = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'pricelisthead';
        $head = file_get_contents($filehead);
        $head = json_decode($head);
        $filecount = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'pricelistcount';
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
        $csv    = array_map("array_combine", array_fill(0, count($csv), $head), $csv);

        $csvResult 		 = array_map("array_combine", array_fill(0, count($csv), $head), $csv);
        $fieldName 		 = implode(",", $fieldName);
        
        $saveTempProduct = $this->_saveTemp($csvResult, $fieldName);
        $records  	= $this->_coreSession->getRecords();	
		$status  	= $this->_coreSession->getStatussave();
		$this->_coreSession->setsavecount($records['Savecount']); 
        file_put_contents($filecount,$records['Savecount']);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Import Price List Result'));
        return $resultPage;
    }

     protected function _saveTemp($csvResult, $fieldName)
	{
		try{
			$count 			= 0;
			$countSave		= 0;
			$countBreak 	= 15;
			$alreadysave	= 0;
			$getData 		= array();
			$resData		= array();
			$tablename 		= self::TABLE_NAME_VALUE;
			$lastIncrement	= null;
            
            $coreSession = $this->_objectManager->get('\Magento\Framework\Session\SessionManagerInterface');
            $directorylist = $this->_objectManager->get('\Magento\Framework\App\Filesystem\DirectoryList');
            $coreSession->unsRecords();
			$records		= count($csvResult);
			$dataSave = null;
            $filecount = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'pricelistcount';
            $SaveCount = file_get_contents($filecount);
            
            if(empty($SaveCount)):
                $SaveCount = 0;
            endif;
			foreach($csvResult as $_key=>$_value):
				$fieldValue 					= null;
				$getData  						= null;
				$getData['price_id']			= null;
				$getData['price_level_id']		= null;
				$getData['from_date']			= null;
				$getData['to_date']				= null;
				$getData['limited_days']		= null;
				$getData['limited_time_from']	= null;
				$getData['limited_time_to']		= null;
				$getData['company'] 			= null;
				$getData['active']				= 0;
				$name 							= null;
				
			if($count >= $SaveCount):
				foreach($_value as $key=>$value):
					if ($key == 'price_id'):
						$getData['price_id'] = $value;
						$name = $value;
					elseif ($key == 'name'):
						$getData['name'] = $value;
					elseif ($key == 'company'):
						$remove[] = "'";
						$remove[] = '"';
						$remove[] = "-";
						$getData['company'] = str_replace($remove, " ",$value);
					elseif ($key == 'price_level_id'):
						$getData['price_level_id'] = $value;
						$keyCode = $getData['price_level_id'];
					elseif ($key == 'active'):
						if(strtolower($value) == 'yes'):
							$getData['active'] = 1;
						endif;
					elseif ($key == 'from_date'):
						$getData['from_date'] = $value;
					elseif ($key == 'to_date'):
						$getData['to_date'] = $value;
					elseif ($key == 'limited_days'):
						$getData['limited_days'] = $value;
					elseif ($key == 'limited_time_from'):
						$getData['limited_time_from'] = date("H:i", strtotime($value));
					elseif ($key == 'limited_time_to'):
						$getData['limited_time_to'] = date("H:i", strtotime($value));
					elseif ($key == 'from_qty'):
						$getData['from_qty'] = $value;
					elseif ($key == 'to_qty'):
						$getData['to_qty'] = $value;
					endif;
					
					//$fieldValue[] = "'".$value."'";	
					if(!empty($value)):
						$fieldValue[] = "'".$value."'";	
					endif;
				endforeach;
				
				if(!empty($keyCode)):
			
						$resData[] =	$this->_saveData($getData);
						
				endif;
                
				$dataSave = true;
				$countSave++;	
				$coreSession->setRecordsave($resData);
				
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
						
		}catch(\Exception $e){
            $this->messageManager->adderror($e->getMessage());
            $this->_logger->error($e->getMessage().' LINE 192');
		}
		
		return $this;
	}
	
    protected function _getConnection()
    {
		$connection = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        return $connection;
    }
	
	protected function _isprice_level_id($keyCode)
	{
		$sql 				= "SELECT id FROM rra_pricelevelmaster WHERE price_level_id LIKE '$keyCode'";	
		$AccIdresult 		= $this->_getConnection()->fetchRow($sql);					
		return $AccIdresult['id'];
	}	
	protected function _iscompany_id($keyCode)
	{
		try{
			$sql 				= "SELECT customer_group_id FROM customer_group WHERE company_code LIKE '$keyCode'";	
			$AccIdresult 		= $this->_getConnection()->fetchRow($sql);					
		}catch (\Exception $e){
            $this->_logger->error($e->getMessage().'LINE 212');
		}
		return $AccIdresult['customer_group_id'];
	}
	protected function _isCustomerGroupExist($keyCode)
	{
		try{
			$sql 				= "SELECT count(*) as customerGroupCount FROM customer_group WHERE customer_group_code LIKE '$keyCode'";	
			$AccIdresult 		= $this->_getConnection()->fetchRow($sql);					
		}catch (\Exception $e){
            $this->_logger->error($e->getMessage().'LINE 212');
		}
		return $AccIdresult['customerGroupCount'];
	}
	
	protected function _getskulist($pricelist_id)
	{
		$SKUs			= null;
		$sql 			= "SELECT * FROM rra_pricelistproduct WHERE pricelist_id LIKE '$pricelist_id'";
		$AccIdresult 	= $this->_getConnection()->fetchAll($sql);	
		return $AccIdresult;
	}


	protected function _issku($keyCode, $name, $sku)
	{
		$sku 			= md5($sku);
		$sql 			= "SELECT rule_id FROM catalogrule WHERE price_level_id LIKE '$keyCode' AND  name LIKE '$name' AND prod_sku='$sku'";	
        $AccIdresult 	= $this->_getConnection()->fetchRow($sql);
      
		if(empty($AccIdresult['rule_id'])):
			$response = false;
		else:
			$response = true;
		endif;
		return $response;
	}
    /**
     * @return bool
     */
    protected function _saveData($getData)
	{
        $coreSession = $this->_objectManager->get('\Magento\Framework\Session\SessionManagerInterface');
		$resData				= array();
        $dataGet  				= $this->_getskulist($getData['price_id']);
		$price_level_id  		= $this->_isprice_level_id($getData['price_level_id']);
        $company_id  			= $this->_iscompany_id($getData['price_level_id']);
       
		$discountEnable 		= 0;
		$discountEnableAmount 	= 0;
		$sub_simple_action 		= null;	
		$from_date 				= strtotime($getData['from_date']);
		$to_date 				= strtotime($getData['to_date']);
		$from_qty 				= str_replace(',','',$getData['from_qty']);
		$to_qty 				= str_replace(',','',$getData['to_qty']);
		$count 					= 0;
		$sku 					= null;
        $fields = array();
        $user                   = $this->userSession->getUser(); 
        $userId                 = $user->getUserId();
        $userUsername           = $user->getUsername();
        $msg_stat = '';
        $disp= '';
		if(empty($company_id)):
			$company_id = 0;
		endif;
		try {
			$skuList = array();
			foreach ($dataGet as $value) {
				if(!empty($value['product_sku'])):
					$sku .= $value['product_sku'].",";
					$skuList[] = $value['product_sku'];
				endif;
            }
            
				$name 				= $getData['price_id'];
				$description 		= $getData['name'];
				$websiteId 			= 1; 
				$customerGroupId 	= $company_id; 
				$actionType 		= 'by_percent';
				$discountEnable 	= 0;
                $discount			= 0;
                
				$uploaded_by        = $userUsername;
				if($this->_isCustomerGroupExist(trim($getData['company'])) < 1){
					$resData[] = 'Customer group <strong><span style="color:red;">'.$getData['company'] .'</span></strong> does not  Exist!!!';
						$coreSession->setStatussave(0);
						$this->_logger->error(sprintf($getData['price_id'].' - '. $getData['price_level_id'], null, date("Y-m-d")). ' LINE 337');
				}else{
					if($this->_issku($price_level_id, $name, $sku) == false){
						if(count($skuList) == 0){
							$resData[] = $getData['price_id'] .' - '.$getData['price_level_id'].' - <span style="color:red;">No existing SKU found in Product Price List!</span>';
						}elseif(count($skuList) > 0){
							$fields['price_id']       = $getData['price_id'];
							$fields['name']           = $getData['name'];
							$fields['company']        = $getData['company'];
							$fields['price_level_id'] = $price_level_id;
							$fields['from_date']      = $from_date;
							$fields['to_date']        = $to_date;
							$fields['limited_days']   = strtolower($getData['limited_days']);
							$fields['limited_time_from']= $getData['limited_time_from'];
							$fields['limited_time_to']  = $getData['limited_time_to'];
							$fields['active']           = ($getData['active']==1)?'YES':'NO';
							$fields['uploaded_by']      = $uploaded_by;
						
						
							$res = $this->_getConnection()->insert('wspi_pricelist', $fields);

							$catalogPriceRule = $this->_objectManager->create('Magento\CatalogRule\Model\Rule');
							$catalogPriceRule->setName($name)
											->setDescription($description)
											->setIsActive(($getData['active']==1)?1:0)
											->setWebsiteIds(array($websiteId))
											->setCustomerGroupIds(array($customerGroupId))
											->setFromDate(date('Y-m-d',$from_date))
											->setToDate(date('Y-m-d',$to_date))
											->setSortOrder('')
											->setfrom_qty($from_qty)
											->setto_qty($to_qty)
											->setprice_level_id($price_level_id)
											->setlimit_days(strtolower($getData['limited_days']))
											->setlimit_time_from(date('H:i:s',strtotime($getData['limited_time_from'])))
											->setlimit_time_to(date('H:i:s',strtotime($getData['limited_time_to'])))
											->setSimpleAction($actionType)
											->setStopRulesProcessing(0)
											->setDiscountAmount($discount)
											->setprod_sku(md5($sku));
							$skuCondition = $this->_objectManager->create('Magento\CatalogRule\Model\Rule\Condition\Product')
											->setType('Magento\CatalogRule\Model\Rule\Condition\Product')
											->setAttribute('sku')
											->setOperator('()')
											->setValue(substr($sku, 0, -1));
												//*** Set for is one of ***//									
												//->setOperator('()')
							
							$catalogPriceRule->getConditions()->addCondition($skuCondition);
							
							$catalogPriceRule->save();
							
							$resData[] = $getData['price_id'] .' - '.$getData['price_level_id'].' - <span style="color:green;">Success!</span>';
							$coreSession->setStatussave(1);
							$this->_logger->error(sprintf($getData['price_id'].' - '. $getData['price_level_id'], null, date("Y-m-d")). ' LINE 337');
						}

						

					}else{

						$resData[] = $getData['price_id'] .' - '.$getData['price_level_id'].' - <span style="color:red;">Exist!</span>';
						$coreSession->setStatussave(0);
						$this->_logger->error(sprintf($getData['price_id'].' - '. $getData['price_level_id'], null, date("Y-m-d").' LINE 343'));

					}
				}
			
		}catch(Exception $e){
          
				$this->messageManager->addError(__($e->getMessage()));
				$this->_logger->error($e->getMessage());
		}
		return $resData;
	
	}
    protected function _isAllowed()
    {
        return true;
    }
}
