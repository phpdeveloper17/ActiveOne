<?php

/**
 * Grid Admin Cagegory Map Record Save Controller.
 * @category  Unilab
 * @package   Unilab_Grid
 * @author    Unilab
 * @copyright Copyright (c) 2010-2016 Unilab Software Private Limited (https://Unilab.com)
 * @license   https://store.Unilab.com/license.html
 */
namespace Unilab\Benefits\Controller\Adminhtml\CompanyBranchController;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Unilab\Grid\Model\GridFactory
     */
    var $gridFactory;
    protected $_storeManager;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Unilab\Grid\Model\GridFactory $gridFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Unilab\Benefits\Model\CompanyBranchFactory $gridFactory,
        \Magento\Store\Model\StoreFactory $storeManager
    ) {
        parent::__construct($context);
        $this->gridFactory = $gridFactory;
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
        
        if (!$data) {
            $this->_redirect('unilab_benefits/CompanyBranchController/create');
            return;
        }
        try {
            // var_dump($data);
            $rowData = $this->gridFactory->create();
            $rowData->setData($data);
            if (isset($data['id'])) {
                $rowData->setId($data['id']);
            }
            else {
                $rowData->setCreatedTime(date('Y-m-d H:i:s'));
            }
            $rowData->setUpdateTime(date('Y-m-d H:i:s'));
            $rowData->save();
            $this->messageManager->addSuccess(__('Company branch has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('unilab_benefits/CompanyBranchController');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
