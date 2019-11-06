<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Customers\Controller\Adminhtml\Group;

use Magento\Framework\Exception\NoSuchEntityException;

class MassDelete extends \Unilab\Customers\Controller\Adminhtml\Group
{
    /**
     * Delete customer group.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $deleteIds = $this->getRequest()->getParam('customer_group_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!is_array($deleteIds) || empty($deleteIds)) {
            $this->messageManager->addError(__('Please select item(s).'));
        } else {
            try {
                $recordDeleted = 0;
                foreach ($deleteIds as $itemId) {
                    $this->groupRepository->deleteById($itemId);
                    $recordDeleted++;
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', $recordDeleted)
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');      
    }
}
