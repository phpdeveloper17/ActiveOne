<?php

namespace Unilab\Benefits\Controller\Validate;

class CheckId extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $resourceConnection;
    protected $customerModel;
    protected $customerGroup;
    protected $groupRepository;
    protected $storeManager;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Customer\Model\Customer $customerModel,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Customer\Model\Group $customerGroup,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resourceConnection = $resourceConnection;
        $this->customerModel = $customerModel;
        $this->customerGroup = $customerGroup;
        $this->storeManager = $storeManager;
        $this->groupRepository = $groupRepository;
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $request = $this->getRequest()->getPostValue();
        $emp_id = $request['emp_id'];
        $response = [];
        $connection = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);

        // $connection->beginTransaction();

        $sql = $connection->select()->from('customer_entity_varchar', array('*'))
            ->where('attribute_id=?', 139)
            ->where('value=?', $emp_id);

        $row_data = $connection->fetchRow($sql);
        $entity_id = $row_data['entity_id'];
        
        $customer = $this->customerModel->load($entity_id)->getData();
        $customer_group_id = $this->customerModel->load($entity_id)->getGroupId();
        $customer_group = $this->customerGroup->load($customer_group_id);

        $sql2 = $connection->select()->from('customer_entity', array('*'))->where('entity_id=?', $entity_id);
        $customer_entity = $connection->fetchRow($sql2);
        
        if (empty($emp_id) || $emp_id == "") {
            $response['success'] = false;
            $response['message'] = 'Employee ID is required';
        } else if (count($customer_entity) > 1) {
            if ($customer['active'] == true) 
            {
                if($customer['accept_updated_privacy'] == true) 
                {
                        if($customer['employment_status'] == true) 
                        {
                            if($customer_group->getIsActive() == false) 
                            {
                                $response['success'] 		= false;
								$response['status'] 		= true;						
								$response['error'] 			= 'Your Company was deactivated.';
                            }
                            else 
                            {
                                $response['email'] 			= $customer['email'];
								$response['emailencrypt'] 	= md5($customer['email']);
								$response['success'] 		= true;
								$response['status'] 		= true;						
								$response['error'] 			= 'Please enter your password.';
                            }
                        }
                        else 
                        {
                            $response['email'] 		= $customer['email'];					
							$response['success'] 	= true;							
							$response['status'] 	= true;							
							$response['agree'] 		= false;							
							$response['isactive'] 	= $customer['employment_status'];							
							$response['agree_msg'] 	= 'Your account has been expired. Please contact our support.';							
							$response['error'] 		= 'Account Expired/ Deactivated.';
                        }
                } else {
                    $response['email'] 		= $customer['email'];
					$response['success'] 	= true;
					$response['status'] 	= true;
					$response['agree'] 		= false;//$is_agreed;
					$response['isactive'] 	= true;
					$response['error'] 		= 'Please enter your password.';
                }
            } else {
                $response['status'] = false;
                $response['success'] = true;
                $response['created_at'] = $customer['created_at'];
                $response['contact_number'] = isset( $customer['contact_number']) ?  $customer['contact_number'] : '' ;
                $response['pricelevel'] = $customer['price_level'];
                $response['gender'] = $customer['gender'];
                $response['civilstatus'] = isset($customer['civil_status']) ? $customer['civil_status'] : '' ;
                $response['emp_id'] = $customer['employee_id'];
                $response['company_id'] = $customer['group_id'];
                $response['id'] = $entity_id;
                $response['firstname'] = $customer['firstname'];
                $response['lastname'] = $customer['lastname'];
                $response['middlename'] = $customer['middlename'];
            }
        } else {
            $response['success'] 	= false;
			$response['message'] 		= 'No record found.';
        }

        $response['employee'] = $customer;
        $response['group_code_is_active'] = $customer_group->getIsActive();
        
        return $result->setData($response);
    
    }

}
