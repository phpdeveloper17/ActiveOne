<?php
    /**
     * Unilab_Grid Add Row Form Block.
     *
     * @category    Unilab
     *
     * @author      Unilab Software Private Limited
     */
namespace Unilab\Benefits\Block\Adminhtml\EmployeeBenefits;

class Importresult extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    public    $resultdata = ['records'=>'', 'status'=>'','percent'=>'','resData'=>''];
    protected $storeManager;
    protected $customerSession;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry           $registry
     * @param array                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * Initialize Imagegallery Images Edit Block.
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Unilab_Benefits';
        $this->_controller = 'adminhtml_EmployeeBenefits';
       
        parent::_construct();//It sets back, reset, delete, and save buttons with default values
        
        $this->addButton(
            'import_new_emp_benefit',
            [
                'label' => __('Import New Employee Benefit'),
                'onclick' => 'setLocation(\'' . $this->getUrl('unilab_benefits/employeebenefitscontroller/import') . '\')',
                'class' => 'import_new_emp_benefit primary'
            ],
            -1
        );
        $this->buttonList->remove('save');
        $this->buttonList->remove('reset');
        
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $coreSession = $this->_objectManager->create('\Magento\Framework\Session\SessionManagerInterface');
        $records  	= $coreSession->getRecords();
        $status  	= $coreSession->getStatussave();
        if(!empty($records)){
            $percent = $records['Savecount'] / $records['Allrecords'];
            $percent = $percent * 100;
            
            $resData = $coreSession->getRecordsave();
            
            $this->resultdata['records'] = $records;
            $this->resultdata['status'] = $status;
            $this->resultdata['percent'] = $percent;
            $this->resultdata['resData'] = $resData;
        }
        
    }

    /**
     * Retrieve text for header element depending on loaded image.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Import Result');
    }

    /**
     * Check permission for passed action.
     *
     * @param string $resourceId
     *
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    public function getFormKey() {

        return $this->formKey->getFormKey();

    }
    public function getCustomerSession(){
        return $this->customerSession;
    }

    public function getStoreManager(){
        return $this->storeManager;
    }
    public function getRecordsave(){
        return $this->resultdata;
    }
    public function getCoreSessionSave(){
        $coreSession = $this->_objectManager->get('\Magento\Framework\Session\SessionManagerInterface');
        return $coreSession;
    }
}
