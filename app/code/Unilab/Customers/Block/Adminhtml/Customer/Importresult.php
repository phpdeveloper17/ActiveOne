<?php
    /**
     * Unilab_Grid Add Row Form Block.
     *
     * @category    Unilab
     *
     * @author      Unilab Software Private Limited
     */
namespace Unilab\Customers\Block\Adminhtml\Customer;

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
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Registry $registry,
        array $data = [],
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_coreRegistry = $registry;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Initialize Imagegallery Images Edit Block.
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Unilab_Customers';
        $this->_controller = 'adminhtml_customer';

        

        parent::_construct();
        $this->buttonList->update('back', 'onclick', "setLocation('" . $this->getUrl('customer/index/index') . "')");
        $this->buttonList->update('reset', 'onclick', "setLocation('" . $this->getUrl('unilab_customers/customer/import') . "')");
        $this->buttonList->update('reset', 'label', "Import New Customer");
        $this->buttonList->update('reset', 'class', "primary");
        $this->buttonList->remove('save');

        $records  	= $this->customerSession->getRecords();	
        $status  	= $this->customerSession->getStatussave();
        if(!empty($records)){
            $percent = $records['Savecount'] / $records['Allrecords'];
            $percent = $percent * 100;
            
            $resData = $this->customerSession->getRecordsave();
            
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

    public function getMyData(){
        return $this->resultdata;
    }
}
