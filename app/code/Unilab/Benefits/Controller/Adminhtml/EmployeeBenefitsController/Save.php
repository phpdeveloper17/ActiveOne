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

use Magento\Framework\Controller\ResultFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Unilab\Grid\Model\GridFactory
     */
    var $gridFactory;
    protected $resourceConnection;
    protected $coreRegistry;
    protected $backendSession;
    protected $_storeManager;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Unilab\Grid\Model\GridFactory $gridFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Unilab\Benefits\Model\EmployeeBenefitFactory $gridFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\Session $backendSession,
        ResultFactory $resultFactory,
        \Magento\Store\Model\StoreFactory $storeManager
    ) {
        parent::__construct($context);
        $this->gridFactory = $gridFactory;
        $this->resourceConnection = $resourceConnection;
        $this->coreRegistry = $coreRegistry;
        $this->resultFactory = $resultFactory;
        $this->backendSession = $backendSession;
        $this->_storeManager = $storeManager;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        //set Website ID in POST
        $postData = $this->getRequest()->getPostValue();
        $storeIds = implode(',',$postData['webstore_id']);
        $storeData = $this->_storeManager->create()->getCollection()->addFieldToFilter('store_id',['in' => $storeIds]);
        $storeData->getSelect()->group('website_id');
        $websiteId = array_values(array_column($storeData->getData(), 'website_id'));
        $website_ids = implode(',', $websiteId);
        
        $this->getRequest()->setPostValue('webstore_id',$storeIds);
        $this->getRequest()->setPostValue('website_id',$website_ids);

        $data = $this->getRequest()->getPostValue();
        
		$start_date = date('Y-m-d',strtotime($data['start_date']));
		$refresh_date = date('Y-m-d',strtotime($data['refresh_date']));
		
		if($data['refresh_period'] == 142){//Monthly
			$startDateP1month = date("Y-m-d", strtotime('+1 month',strtotime($start_date)));
			if($startDateP1month != $refresh_date){
				$this->messageManager->addError(__('Invalid refresh date base on selected refresh period["Monthly"]'));
				return $this->_redirect('unilab_benefits/EmployeeBenefitsController/index');
			}
		}
		if($data['refresh_period'] == 141){//Semi-Monthly
			$numberofdays = date('t',strtotime($start_date));
			$numberofdaysHalf = $numberofdays / 2;
			$startDataD = date('d',strtotime($start_date));
			//
			if($startDataD <= $numberofdaysHalf){
				$startDateSemi = date('Y-m-',strtotime($start_date)).$numberofdaysHalf;
			}else{
				$startDateSemi = date('Y-m-',strtotime($start_date)).$numberofdays;
			}
			if($startDateSemi != $refresh_date){
				$this->messageManager->addError(__('Invalid refresh date base on selected refresh period["Semi-Monthly"]'));
				return $this->_redirect('unilab_benefits/EmployeeBenefitsController/index');
			}
		}
		if($data['refresh_period'] == 139){//Yearly
			$startDateP1year = date("Y-m-d", strtotime('+1 year',strtotime($start_date)));
			if($startDateP1year != $refresh_date){
				$this->messageManager->addError(__('Invalid refresh date base on selected refresh period["Yearly"]'));
				return $this->_redirect('unilab_benefits/EmployeeBenefitsController/index');
			}
		}
		if ($start_date > $refresh_date) {
			$this->messageManager->addError(__('Please ensure that start date is less than on refresh date'));
			return $this->_redirect('unilab_benefits/EmployeeBenefitsController/index');
		}
        if (!$data) {
            $this->_redirect('unilab_benefits/EmployeeBenefitsController/create');
            return;
        }
        try {
            // var_dump($data);
            $rowData = $this->gridFactory->create();
			$checkOrigData = $rowData->getCollection()
                ->addFieldToFilter('emp_id', $data['emp_id'])
                ->addFieldToFilter('purchase_cap_id', $data['purchase_cap_id'])
                ->count();
			$validate=false;
            $redirect = array();
            if(isset($data['id'])){ // check if id is set, this condition is for edit only
                $checkDataExistEdit = $rowData->getCollection()
                ->addFieldToFilter('emp_id', $data['emp_id'])
                ->addFieldToFilter('purchase_cap_id', $data['purchase_cap_id'])
                ->addFieldToFilter('id', $data['id'])
                ->count();
                if($checkDataExistEdit > 0){ //count 1 > 0
                    $validate=false;
                }elseif($checkOrigData > 0){
                    $validate=true;
                }else{
                    $validate=false;
                }
            }else{
                if($checkOrigData > 0){
                    $validate=true;
                }else{
                    $validate=false;
                }
            }
			
			if($data['refresh_period']==140){
				$this->getRequest()->setPostValue('refresh_date',null);
				$this->getRequest()->setPostValue('start_date',null);
			}
			$data = $this->getRequest()->getPostValue();
			
            $rowData->setData($data);
			
            $save = true;
            
            if (isset($data['id'])) {
                //edit
                $rowData->setId($data['id']);
                if($data['purchase_cap_limit'] == $data['available']){
                    if($data['consumed'] > $data['purchase_cap_limit']){
                        $this->messageManager->addError(__('Consumed amount should not be greater than purchase cap limit'));
					    return $this->_redirect('unilab_benefits/EmployeeBenefitsController/index');
                    }
                }
				if($data['consumed'] == 0 || $data['consumed'] > $data['purchase_cap_limit']){
					$rowData->setAvailable($data['purchase_cap_limit']);
				}
            }
            else {
                //create
                $rowData->setCreatedTime(date('Y-m-d H:i:s'));
                $rowData->setAvailable($data['purchase_cap_limit']);
            }

            $rowData->setUpdateTime(date('Y-m-d H:i:s'));
			
            $connection = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
            $emp_id = $data['emp_id'];
            $selectEntity = $connection->select()->from('customer_entity_varchar', array('entity_id'))->where('value=?',$emp_id); 
                
            $rowEntity = $connection->fetchRow($selectEntity);
            
            if(empty($rowEntity['entity_id'])):
                $save = false;
            endif;
			
            if($save){
				if($validate){
					$this->messageManager->addError(__('Duplicate employee benefits for '.$data['emp_id']));
					$this->_redirect('unilab_benefits/EmployeeBenefitsController/create');
				}else{
				$customerSql = $connection->select()->from('customer_entity', array('firstname','lastname'))->where('entity_id=?',$rowEntity['entity_id']);
				$customerEntity = $connection->fetchRow($customerSql);
				$rowData->setEntityId($rowEntity['entity_id']);
				$rowData->setEmpName($customerEntity['firstname'].' '. $customerEntity['lastname']);
				
                $rowData->save();
				
                $this->messageManager->addSuccess(__('Benefit has been successfully saved.'));
                $this->_redirect('unilab_benefits/EmployeeBenefitsController');
				}
            }else{
                $this->coreRegistry->register('row_data', $rowData);
                $this->messageManager->addError(__("Employee ID $emp_id does not exist."));
                $this->backendSession->setData('create_benefit', $data);

                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setPath('*/*/create', ['params' => $data]);
                
                return $resultRedirect;
            }
            
            
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
            $this->_redirect('unilab_benefits/EmployeeBenefitsController');
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
