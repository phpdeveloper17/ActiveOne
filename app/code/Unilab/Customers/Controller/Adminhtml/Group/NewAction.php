<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Customers\Controller\Adminhtml\Group;

use Magento\Customer\Controller\RegistryConstants;

class NewAction extends \Unilab\Customers\Controller\Adminhtml\Group
{
    /**
     * Initialize current group and set it in the registry.
     *
     * @return int
     */
    protected function _initGroup()
    {
        $groupId = $this->getRequest()->getParam('id');
        $this->_coreRegistry->register(RegistryConstants::CURRENT_GROUP_ID, $groupId);

        return $groupId;
    }

    /**
     * Edit or create customer group.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $groupId = $this->_initGroup();

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Unilab_Customers::customer_group');
        $resultPage->getConfig()->getTitle()->prepend(__('Customer Groups'));
        $resultPage->addBreadcrumb(__('Customers'), __('Customers'));
        $resultPage->addBreadcrumb(__('Customer Groups'), __('Customer Groups'), $this->getUrl('customer/group'));

        if ($groupId === null) {
            $resultPage->addBreadcrumb(__('New Group'), __('New Customer Groups'));
            $resultPage->getConfig()->getTitle()->prepend(__('New Customer Group'));
        } else {
            $resultPage->addBreadcrumb(__('Edit Group'), __('Edit Customer Groups'));
            $resultPage->getConfig()->getTitle()->prepend(
                $this->groupRepository->getById($groupId)->getCode()
            );
        }
        $rowId = (int) $this->getRequest()->getParam('id');
        $rowData = $this->customerGroupsFactory->create();
        if ($rowId) {
            $rowData = $rowData->load($rowId);
        }
        $this->_coreRegistry->register('customer_group_data', $rowData);
        $resultPage->getLayout()->addBlock(\Unilab\Customers\Block\Adminhtml\Group\Edit::class, 'group', 'content')
            ->setEditMode((bool)$groupId);

        return $resultPage;
    }
}
