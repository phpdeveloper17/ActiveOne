<?php
namespace Unilab\Benefits\Controller\Customers;

class ActiveTransaction extends \Magento\Framework\App\Action\Action
{
    protected $coreSession;
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
    protected $customerSession;
    protected $resultPage;
    protected $resultJsonFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Customer\Model\Customer $customerModel,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\ResourceModel\CustomerFactory $customerResourceFactory,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerInterfaceFactory,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Customer\Model\Group $customerGroup,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Encryption\Encryptor $encryptor,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Controller\Result\Redirect $resultPage

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
        $this->customerInterfaceFactory = $customerInterfaceFactory;
        $this->customerResourceFactory = $customerResourceFactory;
        $this->coreSession = $coreSession;
        $this->customerSession = $customerSession;
        $this->resulPage = $resultPage;
        date_default_timezone_set('Asia/Manila');
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $request = $this->getRequest()->getPostValue();
 
        $connection = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $response = [];

        $entity_id 	= $request['tnxid'];
        $cusid 		= $request['cusid'];
        $paymethod 		= $request['paymethod'];

        $this->customerSession->setPaymentMethodTt($paymethod);

        $fields 					= array();
		$fields['is_active']		= 0;
		$where 						= array();
        $where[] 					= $connection->quoteInto('entity_id=?', $cusid);
        $connection->update('rra_emp_benefits', $fields,$where);

        $fields 					= array();
		$fields['is_active']		= 1;
		$where 						= array();
		$where[] 					= $connection->quoteInto('id=?', $entity_id);
        $connection->update('rra_emp_benefits', $fields, $where);
        
        // \Magento\Backend\Model\Session
        // \Magento\Catalog\Model\Session
        // \Magento\Checkout\Model\Session
        // \Magento\Customer\Model\Session
        // \Magento\Newsletter\Model\Session

        $this->coreSession->start();
        $this->coreSession->unsSuccesspage(0);

        $Sqlselect = $connection->select()
				->from('rra_emp_benefits', array('purchase_cap_id AS trans_type_id')) 
				->where('id=?',$entity_id);  
		$transtype = $connection->fetchRow($Sqlselect);		
		
		//$this->changeorderstatus($entity_id);
		
		if ($transtype['trans_type_id'] == 4) {
			$this->customerSession->setClinicPurchase(1);
		}
		else
		{
			$this->customerSession->setClinicPurchase(0);
		}	

		$this->customerSession->setViewModal(true);
        // $this->resultPage->setPath('/');('/');
        return $result->setData('true');
		// $this->_redirect('home');
    }   
}