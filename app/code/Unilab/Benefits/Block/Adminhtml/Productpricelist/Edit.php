<?php
    /**
     * Unilab_Grid Add Row Form Block.
     *
     * @category    Unilab
     *
     * @author      Unilab Software Private Limited
     */
namespace Unilab\Benefits\Block\Adminhtml\Productpricelist;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
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

    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Unilab_Benefits';
        $this->_controller = 'adminhtml_productpricelist';
        parent::_construct();
        if ($this->_isAllowedAction('Unilab_Benefits::add_productpricelist') || $this->_isAllowedAction('Unilab_Benefits::edit_productpricelist')) {
            $this->buttonList->update('save', 'label', __('Save Product Price List'));

            // $this->buttonList->add(
            //     'saveandcontinue',
            //     [
            //         'label' => __('Save and Continue Edit'),
            //         'class' => 'save',
            //         'data_attribute' => [
            //             'mage-init' => [
            //                 'button' => [
            //                     'event' => 'saveAndContinueEdit',
            //                     'target' => '#edit_form'
            //                 ]
            //             ]
            //         ]
            //     ],
            //     -100
            // );

        } else {
            $this->buttonList->remove('save');
        }
        $this->buttonList->remove('reset');
        $this->buttonList->remove('delete');
    }

    /**
     * Retrieve text for header element depending on loaded image.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Add Product Price List');
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

    /**
     * Get form action URL.
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        if ($this->hasFormActionUrl()) {
            return $this->getData('form_action_url');
        }

        return $this->getUrl('*/*/save');
    }

    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('productpricelist/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '']);
    }
}
