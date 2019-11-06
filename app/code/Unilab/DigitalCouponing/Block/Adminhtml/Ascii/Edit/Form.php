<?php
/**
 * Unilab_Banners Add New Row Form Admin Block.
 * @category    Unilab
 * @package     Unilab_Banners
 * @author      Unilab Software Private Limited
 *
 */
namespace Unilab\DigitalCouponing\Block\Adminhtml\Ascii\Edit;

/**
 * Adminhtml Add New Row Form.
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context,
     * @param \Magento\Framework\Registry $registry,
     * @param \Magento\Framework\Data\FormFactory $formFactory,
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $model = $this->_coreRegistry->registry('row_data');
        $form = $this->_formFactory->create(
            ['data' => 
                [
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post'
                ]
            ]
        );

        if ($model->getAsciiId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['class' => 'fieldset-wide']
            );
            $fieldset->addField('id', 'hidden', ['name' => 'id']);

        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['class' => 'fieldset-wide']
            );
        }

        $fieldset->addField(
            'ascii_equivalent',
            'text',
            [
                'name' => 'ascii_equivalent',
                'label' => __('ASCII Equivalent'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'letter',
            'text',
            [
                'name' => 'letter',
                'label' => __('Letter'),
                'required' => true,
            ]
        );
        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
