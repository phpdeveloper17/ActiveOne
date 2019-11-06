<?php

/**
 * Grid Admin Cagegory Map Record Save Controller.
 * @category  Unilab
 * @package   Unilab_Grid
 * @author    Unilab
 * @copyright Copyright (c) 2010-2016 Unilab Software Private Limited (https://Unilab.com)
 * @license   https://store.Unilab.com/license.html
 */
namespace Unilab\Benefits\Controller\Adminhtml\PurchaseCapController;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Unilab\Grid\Model\GridFactory
     */
    var $gridFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Unilab\Grid\Model\GridFactory $gridFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Unilab\Benefits\Model\PurchasecapFactory $gridFactory
    ) {
        parent::__construct($context);
        $this->gridFactory = $gridFactory;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
		
        if (!$data) {
            $this->_redirect('unilab_benefits/PurchaseCapController/create');
            return;
        }
        try {
            
            $rowData = $this->gridFactory->create();
            $checkOrigData = $rowData->getCollection()
                ->addFieldToFilter('purchase_cap_id', $data['purchase_cap_id'])
                ->count();
			$checkOrigData2 = $rowData->getCollection()
                ->addFieldToFilter('purchase_cap_des', strtolower(trim($data['purchase_cap_des'])))
                ->count();
			//echo "<pre>";
			//	print_r($checkOrigData2);
			//echo "</pre>";
			//exit();
            $validate=false;
			$validate2 = false;
            $redirect = array();
            if(isset($data['id'])){ // check if id is set, this condition is for edit only
                $checkDataExistEdit = $rowData->getCollection()
                ->addFieldToFilter('purchase_cap_id', $data['purchase_cap_id'])
                ->addFieldToFilter('main_table.id', $data['id'])
                ->count();
                if($checkDataExistEdit > 0){ //count 1 > 0
                    $validate=false;
                }elseif($checkOrigData > 0){
                    $validate=true;
                }else{
                    $validate=false;
                }
				
				$checkDataExistEdit2 = $rowData->getCollection()
                ->addFieldToFilter('main_table.purchase_cap_des', strtolower(trim($data['purchase_cap_des'])))
                ->addFieldToFilter('main_table.id', $data['id'])
                ->count();
                if($checkDataExistEdit2 > 0){ //count 1 > 0
                    $validate2=false;
                }elseif($checkOrigData > 0){
                    $validate2=true;
                }else{
                    $validate2=false;
                }
            }else{
                if($checkOrigData > 0){ // check if company_code is existing. 
                    $validate=true;
                }else{
                    $validate=false;
                }
				if($checkOrigData2 > 0){ // check if company_code is existing. 
                    $validate2=true;
                }else{
                    $validate2=false;
                }
            }
            if($validate){
                $this->messageManager->addError(__('Purchase Cap '.$data['purchase_cap_id'].' already exist.'));
                $this->_redirect('unilab_benefits/PurchaseCapController/add');
			}else if($validate2){
				$this->messageManager->addError(__('Purchase Cap '.$data['purchase_cap_des'].' already exist.'));
                $this->_redirect('unilab_benefits/PurchaseCapController/add');
            }else{
                $rowData->setData($data);
                if (isset($data['id'])) {
                    $rowData->setId($data['id']);
                }
                else {
                    $rowData = $rowData->load($data['purchase_cap_id'],'purchase_cap_id');

                    if ($rowData->getId()) {
                        $this->messageManager->addError(__('Purchase Cap '.$data['purchase_cap_id'].' already exist.'));
                        $this->_redirect('unilab_benefits/PurchaseCapController/index');
                        return;
                    }
                    $rowData->setCreatedTime(date('Y-m-d H:i:s'));
                }
                $rowData->setUpdateTime(date('Y-m-d H:i:s'));
                $rowData->save();
                $this->messageManager->addSuccess(__('Purchase Cap Limit has been successfully saved.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('unilab_benefits/PurchaseCapController');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
