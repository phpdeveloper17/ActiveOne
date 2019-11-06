<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Customers\Controller\Adminhtml\Group;

use Magento\Customer\Api\Data\GroupInterfaceFactory;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Api\GroupRepositoryInterface;

class Save extends \Unilab\Customers\Controller\Adminhtml\Group
{
    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;
    protected $customerGroupsFactory;
    protected $_storeManager;
    /**
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param GroupRepositoryInterface $groupRepository
     * @param GroupInterfaceFactory $groupDataFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        GroupRepositoryInterface $groupRepository,
        GroupInterfaceFactory $groupDataFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Unilab\Customers\Model\CustomerGroupsFactory $customerGroupsFactory,
        \Magento\Store\Model\StoreFactory $storeManager
    ) {
        $this->dataObjectProcessor = $dataObjectProcessor;
        parent::__construct(
            $context,
            $coreRegistry,
            $groupRepository,
            $groupDataFactory,
            $resultForwardFactory,
            $resultPageFactory,
            $customerGroupsFactory,
            $storeManager
        );
        $this->customerGroupsFactory = $customerGroupsFactory;
        $this->_storeManager = $storeManager;
    }

    /**
     * Store Customer Group Data to session
     *
     * @param array $customerGroupData
     * @return void
     */
    protected function storeCustomerGroupDataToSession($customerGroupData)
    {
        if (array_key_exists('code', $customerGroupData)) {
            $customerGroupData['customer_group_code'] = $customerGroupData['code'];
            unset($customerGroupData['code']);
        }
        $this->_getSession()->setCustomerGroupData($customerGroupData);
    }

    /**
     * Create or save customer group.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Backend\Model\View\Result\Forward
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
       
        $taxClass = (int)$this->getRequest()->getParam('tax_class_id');
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Customer\Api\Data\GroupInterface $customerGroup */
        $customerGroup = null;
        if ($taxClass) {
            $id = $this->getRequest()->getParam('id');
            $resultRedirect = $this->resultRedirectFactory->create();
            try {
              
                $data = $this->getRequest()->getPostValue();
                $groupData = $this->customerGroupsFactory->create();
               
                $chckCcAndCgId = 0;
                $checkCompanyCodeExist = $groupData->getCollection()->addFieldToFilter('company_code', $data['company_code'])->count();
                $validate=false;
                if(isset($data['id'])){ // check if id is set, this condition is for edit only
                    $chckCcAndCgId = $groupData->getCollection()
                    ->addFieldToFilter('company_code', $data['company_code'])
                    ->addFieldToFilter('customer_group_id', $data['id'])
                    ->count();
                    if($chckCcAndCgId > 0){ //count 1 > 0
                        $validate=false;
                    }elseif($checkCompanyCodeExist > 0){
                        $validate=true;
                    }else{
                        $validate=false;
                    }
                }else{
                    if($checkCompanyCodeExist > 0){ // check if company_code is existing. 
                        $validate=true;
                    }else{
                        $validate=false;
                    }
                }
            
                if($validate){
                    $this->messageManager->addError(__('Company Code '.$data['company_code'].' Already Exists!!!'));
                        return $this->resultForwardFactory->create()->forward('new');
                }else{
                    $groupData->setData($data);
                    if (isset($data['id'])) {
                        $groupData->setCustomer_group_id($data['id']);
                    }
                    
                    $groupData->save();
                    $this->messageManager->addSuccess(__('You saved the customer group.'));
                $resultRedirect->setPath('unilab_customers/group');
                }
                

                
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                if ($customerGroup != null) {
                    $this->storeCustomerGroupDataToSession(
                        $this->dataObjectProcessor->buildOutputDataArray(
                            $customerGroup,
                            \Magento\Customer\Api\Data\GroupInterface::class
                        )
                    );
                }
                $resultRedirect->setPath('unilab_customers/group/edit', ['id' => $id]);
            }
            return $resultRedirect;
        } else {
            return $this->resultForwardFactory->create()->forward('new');
        }
    }
}
