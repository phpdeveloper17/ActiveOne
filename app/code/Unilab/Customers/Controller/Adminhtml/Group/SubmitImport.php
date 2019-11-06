<?php
/**
 * @category  Unilab
 * @package   Unilab_Benefits->Productpricelist
 * @author    Kristian Claridad
 */
namespace Unilab\Customers\Controller\Adminhtml\Group;

class SubmitImport extends \Magento\Backend\App\Action
{
    const TABLE_NAME_VALUE      = 'customer_group';
    /**
     * @var \Unilab\Grid\Model\GridFactory
     */
    protected $customerGroupFactory;
    protected $resourceConnection;
    protected $userSession;
    protected $messageManager;
    protected $remoteAddress;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Unilab\Grid\Model\customerGroupFactory $customerGroupFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Customer\Model\Group $customerGroupFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Backend\Model\Auth\Session $userSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
		\Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\App\Filesystem\DirectoryList $directorylist
    ) {
        parent::__construct($context);
        $this->customerGroupFactory = $customerGroupFactory;
        $this->resourceConnection = $resourceConnection;
        $this->userSession = $userSession;
        $this->messageManager = $messageManager;
		$this->remoteAddress = $remoteAddress;
		$this->_coreSession = $coreSession;
        $this->_directorylist = $directorylist;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
		if(!defined('DS')){
            define('DS',DIRECTORY_SEPARATOR);
        }
        $data = $this->getRequest()->getPostValue(); // get form key

        $visitorData = $this->remoteAddress->getRemoteAddress(true);
        $connection = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        // $connection->beginTransaction();


        $fullpath = $_FILES['csv_file']['tmp_name'];
        $filename = $_FILES['csv_file']['name'];
        $size = $_FILES['csv_file']['size'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

		if(strtolower($ext) != 'csv'){
            $this->messageManager->addErrorMessage($filename.' - Invalid file type.');
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*/import');
            return $resultRedirect;
		}
		
        //**Get User Session  
        $user                   = $this->userSession->getUser(); 
        $userId                 = $user->getUserId();
        $userUsername           = $user->getUsername();
        $msg_stat = '';
        $disp= '';
        $data = null;
        /**
        *   Validate csv file
        */
		try{
			$csv    = array_map("str_getcsv", file($fullpath));
			$head   = array_shift($csv);
			
			if(file_exists($this->_directorylist->getPath('var'). DS. 'cache'. DS . 'mage--csv') === FALSE) {
				mkdir($this->_directorylist->getPath('var'). DS. 'cache'. DS . 'mage--csv');
			}
			$filecsv = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'company';
			$filehead = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'companyhead';
			$filecount = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'companycount';
			file_put_contents($filecsv, json_encode($csv));
			file_put_contents($filehead, json_encode($head));
			file_put_contents($filecount, json_encode(0));
            //Count if column is complete
            $hiddenOrEmptyHead = false;
			foreach ($head as $key => $value):
				if(strtolower($value) == 'id'):		  
					$value = 'price_id';			
				endif;
				
				$key 			= strtolower(str_replace(' ', '_', $key));		  
				$head[$key] 	= strtolower(str_replace(' ', '_', $value));
				
				if(!empty($head[$key])):
                    $fieldName[] = $head[$key];
                else:
                    $hiddenOrEmptyHead = true;
				endif;
			
			endforeach;
			
			$required_fields = ['companyid','companyname','tin','tax_type','active','creditstatus','term','contactno','contactname'];
			$missing_fields = [];

			foreach($required_fields as $required_field){
				if(!in_array($required_field, $fieldName)){
					$missing_fields[] = $required_field;
				}
            }
            $countArray = [];
			foreach($csv as $c => $v){
				$countArray[$c] = array_count_values($v);
			}
			$resultRedirect = $this->resultRedirectFactory->create();
            if(empty($csv) || @$countArray[0][''] > 3){
                $this->messageManager->addErrorMessage("Empty/insufficient details please check");
                $resultRedirect->setPath('*/*/import');
                return $resultRedirect;
            }
            if($hiddenOrEmptyHead || count($head) > count($required_fields) ||count($csv[0]) > count($required_fields)){
                $this->messageManager->addErrorMessage("Please remove any invalid or hidden characters in csv file");
				
				$resultRedirect->setPath('*/*/import');
				return $resultRedirect;
            }
			if(count($missing_fields) > 0){
				$this->messageManager->addErrorMessage("The following fields are missing: " . implode(', ',array_unique($missing_fields)). ". Please try again.");
				$resultRedirect = $this->resultRedirectFactory->create();
				$resultRedirect->setPath('*/*/import');
				return $resultRedirect;
			}
		}catch(\Exception $e){
			$this->messageManager->addError($e->getMessage());
		}
        $this->_redirect('unilab_customers/group/importresult');
      
    }

}
