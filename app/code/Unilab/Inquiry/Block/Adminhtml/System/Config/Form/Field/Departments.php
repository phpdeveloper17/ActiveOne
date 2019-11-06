<?php
namespace Unilab\Inquiry\Block\Adminhtml\System\Config\Form\Field;

class Departments extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{

    protected $_cmsblockOptions;

    protected $_renderer;

    // public function __construct(
    //     \Magento\Backend\Block\Template\Context $context,
    //      $renderer
    // ) {
    //     $this->_renderer = $renderer;
    //     parent::__construct($context);
    // }

    public function _construct()
    {
        $this->addColumn('code',
            [
                'label' => __('Code'),
                'style' => 'width: 50px'
            ]
        );

        $this->addColumn('name',
            [
                'label' => __('Department'),
                'style' => 'width:70px'
            ]
        );

        $this->addColumn('email',
            [
                'label' => __('Email'),
                'style' => 'width:80px',
            ]
        );

        $this->addColumn('subject',
            [
                'label' => __('Subject'),
                'style' => 'width:90px',
            ]
        );


        $this->addColumn('template',
            [
                'label' => __('Template'),
                'style' => 'width:90px',
                'renderer' => $this->_renderEmailTemplates()
                // 'renderer' => $this->getLayout()->createBlock(
                //     \Unilab\Inquiry\Block\Adminhtml\System\Config\Form\Renderer\Template::class,
                //     '',
                //     ['data' => ['is_render_to_js_template' => true]]
                // )
            ]
        );

        $this->addColumn('sortorder',
            [
                'label' => __('Sort Order'),
                'style' => 'width:30px'
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Department');
        parent::_construct();
    }

    protected function _renderEmailTemplates(){
        if (!$this->_cmsblockOptions) {


            $this->_cmsblockOptions = $this->getLayout()->createBlock(
                \Unilab\Inquiry\Block\Adminhtml\System\Config\Form\Renderer\Template::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );

            $this->_cmsblockOptions->setInputName('test');

        }
        return $this->_cmsblockOptions;
    }

    //https://magently.com/blog/magento-2-backend-configuration-frontend-model-part-33/

    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $template = $row->getData('template');
        $options = [];

        $key = 'option_' . $this->_renderEmailTemplates()->calcOptionHash($template);
        $options[$key] = 'selected="selected"';
        $row->setData('option_extra_attrs', $options);

        return;
    }

}
