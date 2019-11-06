<?php

namespace Unilab\Benefits\Controller\Validate;

use Magento\Framework\Exception\InvalidEmailOrPasswordException;

class Login extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $resourceConnection;
    protected $customerModel;
    protected $customerGroup;
    protected $groupRepository;
    protected $storeManager;
    protected $customerSession;
    protected $customerAccountManagement;
    protected $customerResourceFactory;
    protected $scopeConfig;
    protected $customerAddress;
    

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Customer\Model\Customer $customerModel,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Customer\Model\Group $customerGroup,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement,
        \Magento\Customer\Model\ResourceModel\CustomerFactory $customerResourceFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\AddressFactory $customerAddress
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resourceConnection = $resourceConnection;
        $this->customerModel = $customerModel;
        $this->customerGroup = $customerGroup;
        $this->storeManager = $storeManager;
        $this->groupRepository = $groupRepository;
        $this->customerSession = $customerSession;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerResourceFactory = $customerResourceFactory;
        $this->scopeConfig = $scopeConfig;
        $this->customerAddress = $customerAddress;
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $request = $this->getRequest()->getPostValue();
        $email = $request['email'];
        $password = $request['password'];
        $response = [];
        $connection = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);

        try {

            $customer = $this->customerAccountManagement->authenticate(
                $request['email'],
                $request['password']
            );
            
            $this->customerSession->setCustomerDataAsLoggedIn($customer);
            
            $groupId = $customer->getGroupId();
            $customer_id = $customer->getId();
            $cityval = $streeteval = $regionidval = $postcodeval = "";
            
            //set Agree on terms to 1
            $this->setAgreeOnTermsStatus($customer_id);
            // $agreeOnTerms = $customer->getCustomAttribute('agree_on_terms')->getValue();
            $getAddressID 	= "SELECT entity_id FROM customer_address_entity WHERE parent_id='$customer_id'";	
			$getRow 		= $connection->fetchRow($getAddressID);
			$entity_id		= $getRow['entity_id'];
			// $entity_type_id	= $getRow['entity_type_id'];
			
			
            if (isset($entity_id)) {
                $sqlDelete 	= "DELETE FROM customer_address_entity_text WHERE entity_id='$entity_id'";
                $connection->query($sqlDelete);
                $sqlDelete 	= "DELETE FROM customer_address_entity_text WHERE entity_id='$entity_id'";
                $connection->query($sqlDelete);
                $sqlDelete 	= "DELETE FROM customer_address_entity WHERE parent_id='$customer_id'";
                $connection->query($sqlDelete);
                // $connection->commit();
            }

			$sql = "SELECT * FROM rra_company_branches WHERE company_id='$groupId' AND shipping_address=1";	
			
            foreach ($connection->fetchAll($sql) as $_addresss) {
                $cityval		= $_addresss['branch_city'];
                $streeteval		= $_addresss['branch_address'];
                $regionidval	= $_addresss['branch_province'];
                $postcodeval	= $_addresss['branch_postcode'];
            }

            if(!empty($streeteval)) {
				$this->saveaddressbillAction($streeteval, $regionidval, $postcodeval, $cityval);
            }

            $response['is_loggedin'] = $this->customerSession->isLoggedIn();
            $response['success'] = true;

        } catch (InvalidEmailOrPasswordException $e) {
            
            $response['success'] = false;
            $response['error'] = $e->getMessage();
        
        }
        
        return $result->setData($response);
    
    }

    public function setAgreeOnTermsStatus($customerId) 
    {
        $website_id = $this->storeManager->getWebsite()->getWebsiteId();
        $store = $this->storeManager->getStore();
        $customerNew = $this->customerModel->load($customerId);
        $customerData = $customerNew->getDataModel();
        $customerData->setCustomAttribute('agree_on_terms', 1);
        $customerNew->updateData($customerData);
        $customerResource = $this->customerResourceFactory->create();
        $customerResource->saveAttribute($customerNew, 'agree_on_terms');
    }

    public function saveaddressbillAction($streeteval, $regionidval, $postcodeval, $cityval)
    {
        $country_code = $this->scopeConfig->getValue('general/country/default', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $customer = $this->customerSession->getCustomer();

        $_custom_address = array (
            'firstname'  => $customer->getFirstname(),
            'lastname'   => $customer->getLastname(),
            'street'     => array (
                '0' => $streeteval,
                '1' => ''
            ),
            'city'       => $cityval,
            'postcode'   => $postcodeval,
            'region_id'  => $regionidval,
            'country_id' => $country_code,
            'telephone'  => $customer->getData('contact_number'),
            'mobile'  => $customer->getData('contact_number'),
            'fax'        => '',
        );

        $customerAdd = $this->customerAddress->create();
        $customerAdd->setIsDefaultBilling('1');
        $customerAdd->setIsDefaultShipping('1');
        $customerAdd->setSaveInAddressBook('1');

        $customerAdd->setData($_custom_address);
        $customerAdd->setCustomerId($customer->getId());

        $customerAdd->save();
    }

}
