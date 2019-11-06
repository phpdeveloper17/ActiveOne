<?php

namespace Unilab\Pricelist\Block\Adminhtml\Pricelist\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;

class Actions extends Generic implements TabInterface
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Tutorial\SimpleNews\Model\Config\Status
     */
    protected $_newsStatus;

   /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param Status $newsStatus
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
       /** @var $model \Tutorial\SimpleNews\Model\News */
        $model = $this->_coreRegistry->registry('row_data');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('pricelist_');
        // $form->setFieldNameSuffix('pricelist');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Update Prices Using the Following Information')]
        );

        $fieldset->addField(
            'simple_action',
            'select',
            [
                'name'      => 'simple_action',
                'label'     => __('Apply'),
                'id'        => 'simple_action',
                'title'     => __('Apply'),
                'style'     =>'width:70%',
                'values'    => 
                            [
                                'by_percent'    =>'By Percentage of the Original Price',
                                'by_fixed'      =>'By Fixed Amount',
                                'to_percent'    =>'To Percentage of the Original Price',
                                'to_fixed'      =>'To Fixed Amount',
                            ],
                'required'  => false,
            ]
        );
        $fieldset->addField(
            'discount_amount',
            'text',
            [
                'name'      => 'discount_amount',
                'label'     => __('Discount Amount'),
                'id'        => 'discount_amount',
                'title'     => __('Discount Amount'),
                'style'     =>'width:70%',
                'class'     => 'required-entry',
                'required'  => true,
            ]
        );
        $fieldset->addField(
            'stop_rules_processing',
            'select',
            [
                'name'      => 'stop_rules_processing',
                'label'     => __('Stop Further Rules Processing'),
                'id'        => 'stop_rules_processing',
                'title'     => __('Stop Further Rules Processing'),
                'style'     =>'width:70%',
                'values'    => [1 =>'Yes', 0=>'No'],
                'required'  => false,
            ]
        );
        
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Actions');
    }
 
    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Actions');
    }
 
    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }
 
    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
