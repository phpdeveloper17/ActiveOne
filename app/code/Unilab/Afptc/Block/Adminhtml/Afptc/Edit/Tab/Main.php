<?php

namespace Unilab\Afptc\Block\Adminhtml\Afptc\Edit\Tab;

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
        
        $store_ids = $objectManager->create('Magento\Store\Model\System\Store');
        $store_ids_array = array();
        $store_ids_array = array('value'=>array('label'=>'All Store Views','value'=>0));
        foreach($store_ids->toOptionArray() as $r){
            $store_ids_array[] = $r;
        }
       
        $groupOptions = $objectManager->get('\Magento\Customer\Model\ResourceModel\Group\Collection')->toOptionArray();
        $weekdays = $objectManager->get('Unilab\Afptc\Model\Source\Days')->toOptionArray();
       
        $timezone = $objectManager->get('Unilab\Afptc\Model\Source\TimeFormatAmPm')->toOptionArray();
        $pricelevel = $objectManager->get('Unilab\Afptc\Model\Source\Pricelevel')->toOptionArray();
        
        $yesno = $objectManager->get('Magento\Config\Model\Config\Source\Yesno')->toOptionArray();

        $model = $this->_coreRegistry->registry('awafptc_rule');
      
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('afptc__');
        if ($model->getId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Edit Rule'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
            $fieldset->addField('rule_id', 'hidden', ['name' => 'rule_id']);
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('New Rule'), 'class' => 'fieldset-wide']
            );
        }

        
        $fieldset->addField(
            'name',
            'text',
            [
                'name'      => 'name',
                'label'     => __('Rule Name'),
                'id'        => 'name',
                'title'     => __('Rule Name'),
                'style'     =>'width:70%',
                'class'     => 'required-entry',
                'required'  => true,
            ]
        );

        $fieldset->addField(
            'description',
            'textarea',
            [
                'name'      => 'description',
                'label'     => __('Description'),
                'title'     => __('Description'),
                'required'  => false,
                'style'     =>'width:70%;height:200px',
                'size'      =>10,
                // 'values'    => $websitelist,
                'disabled'  => false
            ]
        );
        $fieldset->addField(
            'status',
            'select',
            [
                'name'      => 'status',
                'label'     => __('Status'),
                'id'        => 'status',
                'title'     => __('Status'),
                'style'     =>'width:70%',
                'values'    => ['1' =>'Enabled', '0'=>'Disabled'],
                'required'  => false,
            ]
        );
        $fieldset->addField(
            'store_ids',
            'multiselect',
            [
                'name'      => 'store_ids[]',
                'label'     => __('Store View'),
                'title'     => __('Store View'),
                'required'  => true,
                'style'     =>'width:100%;height:250px',
                'size'      =>10,
                'values'    => $store_ids_array,
                'disabled'  => false

            ]
        );
        $fieldset->addField(
            'customer_groups',
            'multiselect',
            [
                'name'      => 'customer_groups[]',
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
            'start_date',
            'date',
            [
                'name'      => 'start_date',
                'label'     => __('From Date'),
                'id'        => 'start_date',
                'title'     => __('From Date'),
                'style'     =>'width:70%',
                'date_format' => 'MM/dd/yyyy',
                'time_format' => 'hh:mm:ss',
                'required'  => false,
            ]
        );
        $fieldset->addField(
            'end_date',
            'date',
            [
                'name'      => 'end_date',
                'label'     => __('To Date'),
                'id'        => 'end_date',
                'title'     => __('To Date'),
                'style'     =>'width:70%',
                'date_format' => 'MM/dd/yyyy',
                'time_format' => 'hh:mm:ss',
                'required'  => false,
            ]
        );
        
        $fieldset->addField(
            'show_popup',
            'select',
            [
                'name'      => 'show_popup',
                'label'     => __('Show Pop Up on adding Item(s) to Cart'),
                'id'        => 'show_popup',
                'title'     => __('Show Pop Up on adding Item(s) to Cart'),
                'style'     =>'width:70%',
                'values'    => [1 =>'Yes', 0=>'No'],
                'required'  => false,
            ]
        );
        $fieldset->addField(
            'priority',
            'text',
            [
                'name'      => 'priority',
                'label'     => __('Priority'),
                'id'        => 'priority',
                'title'     => __('Priority'),
                'style'     =>'width:70%',
                'class'     => 'required-entry',
                'required'  => false,
                'note'      => 'Rules with greater priority are processed first',
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
