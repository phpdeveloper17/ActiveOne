<?php
namespace Unilab\Benefits\Controller\Customers;

class Save extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $resourceConnection;
    protected $customerModel;
    protected $customerFactory;
    protected $customerRepository;
    protected $customerGroup;
    protected $groupRepository;
    protected $storeManager;
    protected $encryptor;
    protected $customerInterfaceFactory;
    protected $customerResourceFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Customer\Model\Customer $customerModel,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\ResourceModel\CustomerFactory $customerResourceFactory,
        // \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerInterfaceFactory,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Customer\Model\Group $customerGroup,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Encryption\Encryptor $encryptor
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resourceConnection = $resourceConnection;
        $this->customerModel = $customerModel;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->customerGroup = $customerGroup;
        $this->storeManager = $storeManager;
        $this->groupRepository = $groupRepository;
        $this->encryptor = $encryptor;
        // $this->customerInterfaceFactory = $customerInterfaceFactory;
        $this->customerResourceFactory = $customerResourceFactory;
        date_default_timezone_set('Asia/Manila');
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $request = $this->getRequest()->getPostValue();
        $connection = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $response = [];

        // $connection->beginTransaction();

        $id 			= $request['id'];
        $emp_id 		= $request['emp_id'];
        $firstname 		= $request['firstname'];
        $lastname 		= $request['lastname'];
        $email 			= $request['email'];
        $cemail 		= $request['cemail'];
        $bday 			= $request['bday'];
        $hiredate 		= $request['hiredate'];
        $uploaded_by 	= $request['uploaded_by'];
        $created_at 	= $request['created_at'];
        $contact_number = $request['contact_number'];
        $company_id 	= $request['company_id'];
        $price_level 	= $request['price_level'];
        $gender 		= $request['gender'];
        $civil_status 	= $request['civil_status'];
        $middlename 	= $request['middlename'];
        $emp_password 	= $request['emp_password'];

        $regdate = date("m/d/Y");

        $dob = date("m/d/Y", strtotime($bday));
        $doh = date("m/d/Y", strtotime($hiredate));

        // $connection->commit();

        $isCustomerExists = $this->checkIfCustomerExists($email);

        if (!$email) {
            $response['success'] 	= false;
            $response['error'] 		= 'Email is required';

            return $result->setData($response);
        } elseif ($isCustomerExists) {
            $response['success'] 	= false;
            $response['error'] 		= 'This email ' . $email . ' is already activated.';

            return $result->setData($response);
        } elseif ($email != $cemail) {
            $response['success'] 	= false;
            $response['error'] 		= 'Email not matched!';

            return $result->setData($response);
        } elseif (empty($bday)) {
            $response['success'] 	= false;
            $response['error'] 		= 'Birth date is required';

            return $result->setData($response);
        } elseif (empty($hiredate)) {
            $response['success'] 	= false;
            $response['error'] 		= 'Hired date is required';

            return $result->setData($response);
        } elseif (empty($contact_number)) {
            $response['success'] 	= false;
            $response['error'] 		= 'Contact number is required';

            return $result->setData($response);
        } 
        else {
            $website_id = $this->storeManager->getWebsite()->getWebsiteId();
            $store = $this->storeManager->getStore();
            // $customer = $this->customerFactory->create()->load($id);
            // $customer = $this->customerModel->load($id);
            $customerData = $this->customerModel->load($id);
            
            try {
                $is_valid = true;

                if ($customerData->getId()) {
                    $customerData = $this->customerModel->load($id);
                    $customerData->setEntityId($id);
                    $customerData->setWebsiteId($website_id);
                    $customerData->setStore($store);
                    $customerData->setFirstname($firstname);
                    $customerData->setLastname($lastname);
                    $customerData->setMiddlename($middlename);
                    $customerData->setEmail($email);
                    $customerData->setDob($dob);
                    $customerData->setGender($gender);
                    $customerData->setGroupId($company_id);
                    $customerData->setPassword($emp_password);
                    $customerData->save();

                    $customer = $this->customerRepository->getById($id);
                    $customer->setCustomAttribute('account_id', $emp_id);
                    $customer->setCustomAttribute('contact_number', $contact_number);
                    $customer->setCustomAttribute('date_hired', $doh);
                    $customer->setCustomAttribute('price_level', $price_level);
                    $customer->setCustomAttribute('active', 1);
                    $customer->setCustomAttribute('civil_status', $civil_status);
                    $customer->setCustomAttribute('agree_on_terms', 0);
                    $customer->setCustomAttribute('accept_updated_privacy', 0);
                    $customer->setCustomAttribute('employment_status', 1);
                    $customer->setCustomAttribute('date_registered', $regdate);
                    $customer->setCustomAttribute('employee_id', $emp_id);
                    $this->customerRepository->save($customer);


                    $response['customer'] = $customerData->getId();
                    $response['update'] 	= $updated_customer;
                    $response['success'] 	= true;
                    $response['error'] 		= '!Your Account was successfully updated. Please check your email [' . $email . '] to set your password. Thank you!';
                } else {
                    $response['success'] 	= false;
                    $response['error'] 		= 'Please provide the correct information at-least one of these:  Date Hired, Birth Date or Email Address.';
                }
            } catch (\Exception $e) {
                // $this->insertupdatecustomeremail($id, $dob, $doh, $contact_number);
                // $this->setNewPassword($email);
                $response['success'] 	= true;
                $response['error'] 		= 'Your Account was successfully updated. Please check your email [ ' . $email . ' ] to set your password. Thank you!';
            }
        }
        $response['isActive'] = $isCustomerExists;
        $response['request'] = $request;
        return $result->setData($response);
    }

    public function checkIfCustomerExists($email)
    {
        $website_id = $this->storeManager->getWebsite()->getWebsiteId();
        $customer = $this->customerModel;
        $customer->setWebsiteId($website_id);
        $customer->loadByEmail($email);
        // $customer->load($customer->getId())->getData();

        // if (!empty($customer)) {
        //     return $customer['active'];
        // }
        if($customer->getId())
        {
            return $customer['active'];
        }

        return false;
    }

    public function updateCustomer($uploaded_by, $created_at, $email, $emp_id)
    {
        $connection = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        // $connection->beginTransaction();
        $fields = [];
        $fields['account_id'] = $emp_id;
        $fields['created_at'] = $created_at;
        $fields['updated_at'] = date('Y-m-d H:i:s');
        $where = [];
        $where[] = $connection->quoteInto('email =?', $email);
        $connection->update('customer_entity', $fields, $where);
        // $connection->commit();

        return $emp_id;
    }

    public function insertCustomer($customer_id, $emp_id, $doh, $contact_number, $company_id, $dob, $id)
    {
        $connection = $connection = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        // $connection->beginTransaction();

        $fields = [];
        $fields['entity_id'] = $customer_id;
        // $fields['purchase_cap_limit'] = 200000;
        $where = [];
        $where[] = $connection->quoteInto('emp_id =?', $emp_id);
        $connection->update('rra_emp_benefits', $fields, $where);
        // $connection->commit();
    }

    public function deleteCustomer($id)
    {
        $connection = $connection = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        // $connection->beginTransaction();

        $where 			= 	[];
        $where[] 		= 	$connection->quoteInto('entity_id =?', $id);
        $connection->delete('customer_entity', $where);
        // $connection->commit();

        $where 			= 	[];
        $where[] 		= 	$connection->quoteInto('entity_id =?', $id);
        $connection->delete('customer_entity_varchar', $where);

        $where 			= 	[];
        $where[] 		= 	$connection->quoteInto('entity_id =?', $id);
        $connection->delete('customer_entity_int', $where);

        $where 			= 	[];
        $where[] 		= 	$connection->quoteInto('entity_id =?', $id);
        $connection->delete('customer_entity_datetime', $where);

        $where 			= 	[];
        $where[] 		= 	$connection->quoteInto('entity_id =?', $id);
        $connection->delete('customer_entity_text', $where);
        // $connection->commit();
    }

    public function insertupdatecustomeremail($id, $dob, $doh, $contact_number)
    {
        $connection = $connection = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        // $connection->beginTransaction();

        $fields 					= array();
		$fields['value']			= $dob;
		$where 						= array();
		$where[] 					= $connection->quoteInto('attribute_id =?', 11);
		$where[] 					= $connection->quoteInto('entity_id =?', $id);
		$connection->update('customer_entity_datetime', $fields, $where);
		// $connection->commit();		

		
		// Update data to customer_entity_varchar Date Hired
		$fields 					= array();
		$fields['value']			= $doh;
		$where 						= array();
		$where[] 					= $connection->quoteInto('attribute_id =?', 137);
		$where[] 					= $connection->quoteInto('entity_id =?', $id);
		$connection->update('customer_entity_datetime', $fields, $where);
		// $connection->commit();				
		
		// Update data to customer_entity_varchar Contact Number
		$fields 					= array();
		$fields['value']			= $contact_number;
		$where 						= array();
		$where[] 					= $connection->quoteInto('attribute_id =?', 138);
		$where[] 					= $connection->quoteInto('entity_id =?', $id);
		$connection->update('customer_entity_varchar', $fields, $where);
		// $connection->commit();	
		
		// Update data to customer_entity_varchar set to Active
		$fields 					= array();
		$fields['value']			= 1;
		$where 						= array();
		$where[] 					= $connection->quoteInto('attribute_id =?', 143);
		$where[] 					= $connection->quoteInto('entity_id =?', $id);
		$connection->update('customer_entity_int', $fields, $where);
		// $connection->commit();			
		
		
		//*** Update Account Status to Active
		$fields 				= array();
		$fields['is_active']	= 1;
		$fields['updated_at'] 	= date('Y-m-d H:i:s');
		$where 					= array();
		$where[] 				= $connection->quoteInto('entity_id =?', $id);
		$connection->update('customer_entity', $fields, $where);
		// $connection->commit();		

		return $contact_number;
    }

    public function setNewPassword($email)
    {
        $website_id = $this->storeManager->getStore()->getWebsiteId();
        $customer = $this->customerModel;
        $customer->setWebsiteId($website_id);
        $customer->loadByEmail($email);
        $customer_id = $customer->getId();
        $customer = $this->customerModel->load($customer_id);
        $customer->setPassword('p@ssw0rd123');
        $customer->save();
    }
}
