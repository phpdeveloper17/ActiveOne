<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Customers\Controller\Adminhtml\Customer;

use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class MassDelete
 */
class MassDelete extends \Magento\Customer\Controller\Adminhtml\Index\MassDelete
{

    /**
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        $deleteStatus = true;
        $emId = [];
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $customersDeleted = 0;
        foreach ($collection->getAllIds() as $customerId) {
            $customerData = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($customerId);
            $employee_id = strtoupper(trim($customerData->getEmployee_id()));
            $empBenefits = $this->_objectManager->create("Unilab\Benefits\Model\EmployeeBenefit")
                        ->getCollection()->addFieldToFilter("emp_id",$employee_id)->load()->count();
            
            if ($empBenefits > 0) {
                $deleteStatus = false;
                $emId[] = $employee_id;
            }else{
                $deleteStatus = true;
                $this->customerRepository->deleteById($customerId);
                $customersDeleted++;
            }
        }
        if(!$deleteStatus){
            $this->messageManager->addError(__('Customer could not be deleted, Employee ['.implode(',',$emId).'] benefits record exist!!!'));

        }
        if ($customersDeleted) {
            $this->messageManager->addSuccess(__('A total of %1 record(s) were deleted.', $customersDeleted));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
    }
}
