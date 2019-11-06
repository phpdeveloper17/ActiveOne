<?php

/**
 * Grid Admin Cagegory Map Record Save Controller.
 * @category  Unilab
 * @package   Unilab_Grid
 * @author    Unilab
 * @copyright Copyright (c) 2010-2016 Unilab Software Private Limited (https://Unilab.com)
 * @license   https://store.Unilab.com/license.html
 */
namespace Unilab\Benefits\Controller\Adminhtml\TransactionTypeController;

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
        \Unilab\Benefits\Model\TransactionTypeFactory $gridFactory
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
            $this->_redirect('unilab_benefits/TransactionTypeController/create');
            return;
        }
        try {
            
            $rowData = $this->gridFactory->create();
            $checkOrigData = $rowData->getCollection()
                ->addFieldToFilter('transaction_name', $data['transaction_name'])
                ->addFieldToFilter('tender_type', implode(",",$data['tender_type']))
                ->count();
			$validate=false;
            $redirect = array();
            if(isset($data['id'])){ // check if id is set, this condition is for edit only
                $checkDataExistEdit = $rowData->getCollection()
                ->addFieldToFilter('transaction_name', $data['transaction_name'])
                ->addFieldToFilter('tender_type', implode(",",$data['tender_type']))
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
            if($validate){
                $this->messageManager->addError(__('Duplicate transaction type for '.$data['transaction_name']));
                $this->_redirect('unilab_benefits/TransactionTypeController/create');
            }else{
                if (isset($data['id'])) {
                    $rowData->setId($data['id']);
                }
                else {
                    $rowData->setCreatedTime(date('Y-m-d H:i:s'));
                }

                $tender_type = implode(",",$data['tender_type']);

                $rowData->setTransactionName($data['transaction_name']);
                $rowData->setTenderType($tender_type);
                // $rowData->setTaxClass($data['tax_class']);
                
                $rowData->setUpdatedTime(date('Y-m-d H:i:s'));
                $rowData->save();
                $this->messageManager->addSuccess(__('Transaction type has been successfully saved.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('unilab_benefits/TransactionTypeController');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Unilab_Benefits::save');
    }
}
