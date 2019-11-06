<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Customers\Controller\Adminhtml\Group;

use Magento\Framework\Exception\NoSuchEntityException;

class Delete extends \Unilab\Customers\Controller\Adminhtml\Group
{
    /**
     * Delete customer group.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $this->groupRepository->deleteById($id);
                $this->messageManager->addSuccess(__('You deleted the customer group.'));
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addError(__('The customer group no longer exists.'));
                return $resultRedirect->setPath('unilab_customers/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('unilab_customers/group/edit', ['id' => $id]);
            }
        }
        return $resultRedirect->setPath('unilab_customers/group');
    }
}
