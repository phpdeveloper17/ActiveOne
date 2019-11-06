<?php

namespace Unilab\Pricelist\Block\Adminhtml\Pricelist\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;

class Main extends Generic implements TabInterface
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

        $websitelist = array();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $websitelist = $objectManager->create('Magento\Config\Model\Config\Source\Website')->toOptionArray();
        $groupOptions = $objectManager->get('\Magento\Customer\Model\ResourceModel\Group\Collection')->toOptionArray();
        $weekdays = $objectManager->get('Unilab\Pricelist\Model\Source\Days')->toOptionArray();
       
        $timezone = $objectManager->get('Unilab\Pricelist\Model\Source\TimeFormatAmPm')->toOptionArray();
        $pricelevel = $objectManager->get('Unilab\Pricelist\Model\Source\Pricelevel')->toOptionArray();
        
        $yesno = $objectManager->get('Magento\Config\Model\Config\Source\Yesno')->toOptionArray();

        $model = $this->_coreRegistry->registry('row_data');
        // echo "<pre>";
        //     print_r($model->getData());
        // echo "</pre>";
        // exit();
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('pricelist_');
        if ($model->getId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Edit Price List'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
            $fieldset->addField('rule_id', 'hidden', ['name' => 'rule_id']);
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('New Price List'), 'class' => 'fieldset-wide']
            );
        }

        
        $fieldset->addField(
            'name',
            'text',
            [
                'name'      => 'name',
                'label'     => __('Name'),
                'id'        => 'name',
                'title'     => __('Name'),
                'style'     =>'width:70%',
                'class'     => 'required-entry',
                'required'  => true,
            ]
        );

        $fieldset->addField(
            'websites',
            'multiselect',
            [
                'name'      => 'websites[]',
                'label'     => __('Websites'),
                'title'     => __('Websites'),
                'required'  => true,
                'style'     =>'width:70%;height:250px',
                'size'      =>10,
                'values'    => $websitelist,
                'disabled'  => false
            ]
        );
        $fieldset->addField(
            'customer_group',
            'multiselect',
            [
                'name'      => 'customer_group[]',
                'label'     => __('Customer Groups'),
                'title'     => __('Customer Groups'),
                'required'  => true,
                'style'     =>'width:70%;height:250px',
                'size'      =>10,
                'values'    => $groupOptions,
                'disabled'  => false

            ]
        );

        $fieldset->addField(
            'price_level_id',
            'select',
            [
                'name'      => 'price_level_id',
                'label'     => __('Price Level'),
                'id'        => 'price_level_id',
                'title'     => __('Price Level'),
                'style'     =>'width:70%',
                'values'    => $pricelevel,
                'required'  => false,
            ]
        );
        $fieldset->addField(
            'from_date',
            'date',
            [
                'name'      => 'from_date',
                'label'     => __('From Date'),
                'id'        => 'from_date',
                'title'     => __('From Date'),
                'style'     =>'width:70%',
                'date_format' => 'MM/dd/yyyy',
                'required'  => false,
            ]
        );
        $fieldset->addField(
            'to_date',
            'date',
            [
                'name'      => 'to_date',
                'label'     => __('To Date'),
                'id'        => 'to_date',
                'title'     => __('To Date'),
                'style'     =>'width:70%',
                'date_format' => 'MM/dd/yyyy',
                'required'  => false,
            ]
        );
        $fieldset->addField(
            'limited_days',
            'multiselect',
            [
                'name'      => 'limited_days[]',
                'label'     => __('Limited days only'),
                'title'     => __('Limited days only'),
                'required'  => true,
                'style'     =>'width:70%;height:250px',
                'size'      =>10,
                'values'    => $weekdays,
                'disabled'  => false

            ]
        );
        $fieldset->addField(
            'limited_time_from',
            'select',
            [
                'name'      => 'limited_time_from',
                'label'     => __('Limited Time From'),
                'id'        => 'limited_time_from',
                'title'     => __('Limited Time From'),
                'style'     =>'width:70%',
                'values'    => $timezone,
                'required'  => false,
                'note'      => __('Set 12:00 AM for 24 hours validation'),
            ]
        );
        $fieldset->addField(
            'limited_time_to',
            'select',
            [
                'name'      => 'limited_time_to',
                'label'     => __('Limited Time To'),
                'id'        => 'limited_time_to',
                'title'     => __('Limited Time To'),
                'style'     =>'width:70%',
                'values'    => $timezone,
                'required'  => false,
                'note'      => __('Set 12:00 AM for 24 hours validation'),
            ]
        );
        $fieldset->addField(
            'from_qty',
            'text',
            [
                'name'      => 'from_qty',
                'label'     => __('Limited Qty From'),
                'id'        => 'from_qty',
                'title'     => __('Limited Qty From'),
                'style'     =>'width:70%',
                'class'     => 'required-entry',
                'required'  => false,
            ]
        );
        $fieldset->addField(
            'to_qty',
            'text',
            [
                'name'      => 'to_qty',
                'label'     => __('Limited Qty To'),
                'id'        => 'to_qty',
                'title'     => __('Limited Qty To'),
                'style'     =>'width:70%',
                'class'     => 'required-entry',
                'required'  => false,
            ]
        );
        $fieldset->addField(
            'active',
            'select',
            [
                'name'      => 'active',
                'label'     => __('Active'),
                'id'        => 'active',
                'title'     => __('Active'),
                'style'     =>'width:70%',
                'values'    => [1 =>'Active', 0=>'Inactive'],
                'required'  => false,
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);
        $this->_eventManager->dispatch('adminhtml_promo_catalog_edit_tab_main_prepare_form', array('form' => $form));
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('New Price List');
    }
 
    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('New Price List');
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
