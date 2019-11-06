<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Customers\Controller\Adminhtml\Customer;

use Magento\Framework\Controller\ResultFactory;

class Delete extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * Delete customer action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $formKeyIsValid = $this->_formKeyValidator->validate($this->getRequest());
        $isPost = $this->getRequest()->isPost();
        if (!$formKeyIsValid || !$isPost) {
            $this->messageManager->addError(__('Customer could not be deleted.'));
            return $resultRedirect->setPath('customer/index');
        }

        $customerId = $this->initCurrentCustomer();
        $customerData = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($customerId);
        $employee_id = strtoupper(trim($customerData->getEmployee_id()));
        $empBenefits = $this->_objectManager->create("Unilab\Benefits\Model\EmployeeBenefit")
                    ->getCollection()->addFieldToFilter("emp_id",$employee_id)->load()->count();
        
        if ($empBenefits > 0) {
            $this->messageManager->addError(__('Customer could not be deleted, Employee ['.$employee_id.'] benefits record exist!!!'));
            return $resultRedirect->setPath('customer/index');
        }
        if (!empty($customerId)) {
            try {
                $this->_customerRepository->deleteById($customerId);
                $this->messageManager->addSuccess(__('You deleted the customer.'));
            } catch (\Exception $exception) {
                $this->messageManager->addError($exception->getMessage());
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('customer/index');
    }
}
