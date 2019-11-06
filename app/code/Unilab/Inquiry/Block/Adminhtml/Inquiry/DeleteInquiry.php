<?php
    /**
     * Unilab_Grid Add Row Form Block.
     *
     * @category    Unilab
     *
     * @author      Unilab Software Private Limited
     */
    namespace Unilab\Inquiry\Block\Adminhtml\Inquiry;

class DeleteInquiry extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    protected $storeManager;
    protected $customerSession;
    protected $userSession;
    protected $_department;
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
        \Magento\Backend\Model\Auth\Session $userSession,
        \Unilab\Inquiry\Helper\Data $departmentHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $dateFormatter,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->userSession = $userSession;
        $this->_departmentHelper = $departmentHelper;
        $this->_department = new \Magento\Framework\DataObject();
        $this->_dateFormatter = $dateFormatter;
        $this->_storeManager = $storeManager;
        $this->_backendUrlHelper = $backendUrl;
        parent::__construct($context, $data);
        // $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * Initialize Imagegallery Images Edit Block.
     */
    protected function _construct()
    {
        // $this->_objectId = 'id';
        // $this->_blockGroup = 'Unilab_Inquiry';
        // $this->_controller = 'adminhtml_inquiry';
        // parent::_construct();//It sets back, reset, delete, and save buttons with default values
        // $this->_inquiry = $this->_coreRegistry->registry("row_data");
        // $this->addButton(
        //     'delete_inquiry',
        //     [
        //         'label' => __('Delete'),
        //         'class' => 'delete-inquiry primary',
        //         'onclick' => 'deleteConfirm(\''. __('Are you sure you want to do this?')
        //             .'\', \'' . $this->_backendUrlHelper->getUrl('*/*/deleteinquiry',array(
        //                 'id' => $this->_inquiry->getId(),
        //                 // 'key' => $this->_backendUrlHelper->getSecretKey()
        //             )) . '\')'
        //     ],
        //     -1
        // );
        // //
        // $this->buttonList->remove('save');
        // $this->buttonList->remove('reset');
        // $this->buttonList->remove('delete');
        //
        // $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

    }

    /**
     * Retrieve text for header element depending on loaded image.
     *
     * @return \Magento\Framework\Phrase
     */
    // public function getHeaderText()
    // {
    //     return __('Import Result');
    // }

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


}
