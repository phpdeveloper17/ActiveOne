<?php
/**
 * @category  Unilab
 * @package   Unilab_Benefits
 * @author    Kristian Claridad
 */
namespace Unilab\Benefits\Controller\Adminhtml\Pricelevel;


class SubmitImport extends \Magento\Backend\App\Action
{
    const TABLE_NAME_VALUE      = 'wspi_pricelevel';
    /**
     * @var \Unilab\Grid\Model\GridFactory
     */
    var $gridFactory;
    protected $resourceConnection;
    protected $userSession;
    protected $messageManager;
    protected $remoteAddress;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Unilab\Grid\Model\GridFactory $gridFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Unilab\Benefits\Model\ProductpricelistFactory $gridFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Backend\Model\Auth\Session $userSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\App\Filesystem\DirectoryList $directorylist
    ) {
        parent::__construct($context);
        $this->gridFactory = $gridFactory;
        $this->resourceConnection = $resourceConnection;
        $this->userSession = $userSession;
        $this->messageManager = $messageManager;
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
        
        $fullpath = $_FILES['csv_file']['tmp_name'];
        $filename = $_FILES['csv_file']['name'];
        $size = $_FILES['csv_file']['size'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if(strtolower($ext) != 'csv'){
            $this->messageManager->addErrorMessage($filename.' - Invalid file type.');
            return $this->_redirect('unilab_benefits/pricelevel/import');
        }

        

        //**Get User Session  
        $user                   = $this->userSession->getUser(); 
        $userId                 = $user->getUserId();
        $userUsername           = $user->getUsername();
        $msg_stat = '';
        $disp= '';
        /**
        *   Validate csv file
        */
        try{
            $csv    = array_map("str_getcsv", file($fullpath));
            
            $head   = array_shift($csv);
            
            if(file_exists($this->_directorylist->getPath('var'). DS. 'cache'. DS . 'mage--csv') === FALSE) {
                mkdir($this->_directorylist->getPath('var'). DS. 'cache'. DS . 'mage--csv');
            }
            $filecsv = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'pricelevel';
            $filehead = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'pricelevelhead';
            $filecount = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'pricelevelcount';
            file_put_contents($filecsv, json_encode($csv));
            file_put_contents($filehead, json_encode($head));
            file_put_contents($filecount, json_encode(0));
    
            //Count if column is complete
            $hiddenOrEmptyHead = false;
            foreach ($head as $key => $value):
                if(strtolower($value) == 'id'):
                $value = 'price_level_id';
                endif;
                $key 			= strtolower(str_replace(' ', '_', $key));
                $head[$key] 	= strtolower(str_replace(' ', '_', $value));
                if(!empty($head[$key])):
                    $fieldName[] = $head[$key];
                else:
                    $hiddenOrEmptyHead = true;
                endif;
            endforeach;

            $required_fields = ['price_level_id','name','active'];
            $missing_fields = [];

            foreach($required_fields as $required_field){
                if(!in_array($required_field, $fieldName)){
                    $missing_fields[] = $required_field;
                }
            }
            $resultRedirect = $this->resultRedirectFactory->create();
            if(empty($csv)){
                $this->messageManager->addErrorMessage("Empty details please check");
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
                $resultRedirect->setPath('*/*/import');
                return $resultRedirect;
            }

        }catch(\Exception $e){
            $this->messageManager->addError($e->getMessage());
        }
      return $this->_redirect('unilab_benefits/pricelevel/importresult');
    }

}
