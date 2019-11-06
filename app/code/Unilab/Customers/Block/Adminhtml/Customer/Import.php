<?php
    /**
     * Unilab_Grid Add Row Form Block.
     *
     * @category    Unilab
     *
     * @author      Unilab Software Private Limited
     */
namespace Unilab\Customers\Block\Adminhtml\Customer;

class Import extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry           $registry
     * @param array                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
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
        // $data = array(
        //     'label' =>  'Back',
        //     'onclick'   => 'setLocation(\'' . $this->getUrl('customer/index/index') . '\')',
        //     'class'     =>  'back'
        // );

        // $this->addButton ('my_back', $data, 0, 100,  'header');
        // if ($this->_isAllowedAction('Unilab_Benefits::import')) {
        //     $this->buttonList->update('save', 'label', __('Upload CSV File'));
        // } else {
            $this->buttonList->remove('save');
        // }
             $this->buttonList->remove('reset');
            //  $this->buttonList->remove('back');
    }

    /**
     * Retrieve text for header element depending on loaded image.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Manage Import');
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
}
