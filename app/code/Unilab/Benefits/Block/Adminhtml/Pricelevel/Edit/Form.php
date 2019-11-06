<?php

namespace Unilab\Benefits\Block\Adminhtml\Pricelevel\Edit;

use Magento\Backend\Block\Widget\Form\Generic;

class Form extends Generic
{
   /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context,
     * @param \Magento\Framework\Registry $registry,
     * @param \Magento\Framework\Data\FormFactory $formFactory,
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        // $this->_cityFactory = $cityFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    protected function _prepareForm()
    {

        $websitelist = array();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
     
        $yesno = $objectManager->get('Magento\Config\Model\Config\Source\Yesno')->toOptionArray();

        $model = $this->_coreRegistry->registry('pricelevel_data');
      
        $form = $this->_formFactory->create(
            ['data' => [
                            'id' => 'edit_form',
                            'enctype' => 'multipart/form-data',
                            'action' => $this->getData('action'),
                            'method' => 'post'
                        ]
            ]
        );
       
        $form->setHtmlIdPrefix('pricelist_');
        
        if ($model->getId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Edit Price Level'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
            $fieldset->addField('created_time', 'hidden', ['name' => 'created_time']);
            $fieldset->addField('update_time', 'hidden', ['name' => 'update_time']);
            $model->addData(
                [
                    'created_time' => $model->getCreated_time(),
                    'update_time' => date('Y-m-d h:i:s'),
                ]
            );
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Add New Price Level'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('created_time', 'hidden', ['name' => 'created_time']);
            $fieldset->addField('update_time', 'hidden', ['name' => 'update_time']);

            $model->addData(
                [
                    'created_time' => date('Y-m-d h:i:s'),
                    'update_time' => date('Y-m-d h:i:s'),
                ]
            );
        }

        $fieldset->addField(
            'price_level_id',
            'text',
            [
                'name'      => 'price_level_id',
                'label'     => __('Price Level'),
                'id'        => 'price_level_id',
                'title'     => __('Price Level'),
                'required'  => true,
                'maxlength' => 50
            ]
        );
        $fieldset->addField(
            'price_name',
            'text',
            [
                'name'      => 'price_name',
                'label'     => __('Price Level Name'),
                'id'        => 'price_name',
                'title'     => __('Price Level Name'),
                'class'     => 'required-entry',
                'required'  => true,
                'maxlength' => 255
            ]
        );
        $fieldset->addField(
            'is_active',
            'select',
            [
                'name'      => 'is_active',
                'label'     => __('Active'),
                'id'        => 'active',
                'title'     => __('Active'),
                'values'    => [1 =>'Yes', 0=>'No'],
                'required'  => false,
            ]
        );
        
        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
}

