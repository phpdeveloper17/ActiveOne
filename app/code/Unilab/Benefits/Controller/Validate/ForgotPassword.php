<?php

namespace Unilab\Benefits\Controller\Validate;

class ForgotPassword extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $resourceConnection;
    protected $customerModel;
    protected $customerGroup;
    protected $groupRepository;
    protected $storeManager;
    protected $userHelper;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Customer\Model\Customer $customerModel,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Customer\Model\Group $customerGroup,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\User\Helper\Data $userHelper
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resourceConnection = $resourceConnection;
        $this->customerModel = $customerModel;
        $this->customerGroup = $customerGroup;
        $this->storeManager = $storeManager;
        $this->groupRepository = $groupRepository;
        $this->userHelper = $userHelper;
    }

    public function execute () 
    {
        $result = $this->resultJsonFactory->create();
        $request = $this->getRequest()->getPostValue();
        $email = $request['email'];
        $response = [];

        $website_id = $this->storeManager->getWebsite()->getWebsiteId();
        $store = $this->storeManager->getStore();
        $customer = $this->customerModel->setWebsiteId($website_id)
                    ->loadByEmail($email);

        if($customer->getId()) {
            try {
                $newResetPasswordLinkToken =   $this->userHelper->generateResetPasswordLinkToken(); //$customer->_getHelper('customer')->generateResetPasswordLinkToken();
                $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                $customer->sendPasswordResetConfirmationEmail();
                
                $response['success'] 	= true;			
                $response['error'] 		= 'If there is an account associated with ' . $email .' you will receive an email with a link to reset your password.';
                
            } catch (Exception $exception) {
                $response['success'] 	= false;			
                $response['error'] 		= $exception->getMessage();
                
            }
        }
        else {
            $response['success'] = false;
            $response['error'] = "Email doesn't exists.";
        }

        return $result->setData($response);
    }
}