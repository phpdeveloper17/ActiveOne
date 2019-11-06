<?php

namespace Unilab\Benefits\Block\Adminhtml\Productpricelist\Edit;

use Magento\Backend\Block\Widget\Form\Generic;

class Form extends Generic
{
   /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    protected $_store;
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
        \Unilab\Benefits\Model\Source\Visibility $visibility,
        \Magento\Store\Model\System\Store $store,
        array $data = []
    ) {
        $this->_visibility = $visibility;
        $this->_store = $store;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    protected function _prepareForm()
    {
        $store_ids = $this->_store;
        $store_ids_array = array();
        $store_ids_array = array('value'=>array('label'=>'All Store Views','value'=>0));
        foreach($store_ids->toOptionArray() as $r){
            $store_ids_array[] = $r;
        }
        $websitelist = array();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
     

        $model = $this->_coreRegistry->registry('productpricelist_data');
        $numValueFormatter = [
            'qty_from'          => number_format($model->getQty_from(),2),
            'qty_to'            => number_format($model->getQty_to(),2),
            'unit_price'        => number_format($model->getUnit_price(),2),
            'discount_in_amount'=> number_format($model->getDiscount_in_amount(),2),
            'discount_in_percent'=> number_format($model->getDiscount_in_percent(),2),
        ];
        $model->addData($numValueFormatter);
        
        $form = $this->_formFactory->create(
            ['data' => [
                            'id' => 'edit_form',
                            'enctype' => 'multipart/form-data',
                            'action' => $this->getData('action'),
                            'method' => 'post'
                        ]
            ]
        );

        $form->setHtmlIdPrefix('productpricelist_');
        if ($model->getId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Edit Product Price List'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Add New Product Price List'), 'class' => 'fieldset-wide']
            );
            $model->addData(['visibility' => 4]);//default [Catalog, Search]
        }

        $fieldset->addField(
            'pricelist_id',
            'text',
            [
                'name'      => 'pricelist_id',
                'label'     => __('Price List'),
                'id'        => 'pricelist_id',
                'title'     => __('Price List'),
                'style'     =>'width:70%',
                'required'  => true,
                'maxlength' => 255
            ]
        );
        $fieldset->addField(
            'product_sku',
            'text',
            [
                'name'      => 'product_sku',
                'label'     => __('SKU'),
                'id'        => 'product_sku',
                'title'     => __('SKU'),
                'style'     =>'width:70%',
                'required'  => true,
                'maxlength' => 255
            ]
        );
        $fieldset->addField(
            'product_name',
            'textarea',
            [
                'name'      => 'product_name',
                'label'     => __('Product Name'),
                'id'        => 'product_name',
                'title'     => __('Product Name'),
                'style'     =>'width:70%;height:200px',
                'class'     => 'required-entry',
                'required'  => true,
                'maxlength' => 255
            ]
        );
        $fieldset->addField(
            'qty_from',
            'text',
            [
                'name'      => 'qty_from',
                'label'     => __('Qty From'),
                'id'        => 'qty_from',
                'title'     => __('Qty From'),
                'style'     =>'width:70%',
                'required'  => true,
                'maxlength' => 11
            ]
        );
        $fieldset->addField(
            'qty_to',
            'text',
            [
                'name'      => 'qty_to',
                'label'     => __('Qty To'),
                'id'        => 'qty_to',
                'title'     => __('Qty To'),
                'style'     =>'width:70%',
                'required'  => true,
                'maxlength' => 11
            ]
        );
        $fieldset->addField(
            'unit_price',
            'text',
            [
                'name'      => 'unit_price',
                'label'     => __('Unit Price'),
                'id'        => 'unit_price',
                'title'     => __('Unit Price'),
                'style'     =>'width:70%',
                'required'  => true,
                'maxlength' => 12
            ]
        );
        $fieldset->addField(
            'discount_in_amount',
            'text',
            [
                'name'      => 'discount_in_amount',
                'label'     => __('Discount in Amount'),
                'id'        => 'discount_in_amount',
                'title'     => __('Discount in Amount'),
                'style'     =>'width:70%',
                'required'  => true,
                'maxlength' => 12
            ]
        );
        $fieldset->addField(
            'discount_in_percent',
            'text',
            [
                'name'      => 'discount_in_percent',
                'label'     => __('Discount in Percent'),
                'id'        => 'discount_in_percent',
                'title'     => __('Discount in Percent'),
                'style'     =>'width:70%',
                'required'  => true,
                'maxlength' => 12
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
                'maxlength' => 12
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
                'maxlength' => 12
            ]
        );
        $fieldset->addField(
            'visibility',
            'select',
            [
                'name'      => 'visibility',
                'label'     => __('Visibilty'),
                'id'        => 'visibility',
                'title'     => __('Visibilty'),
                'style'     =>'width:70%',
                'required'  => true,
                'values'    => $this->_visibility->toOptionArray()
            ]
        );
        $typeInputStoreView = $this->_scopeConfig->getValue('webservice/storeviewsetting/typeinput', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $typeInput='select';
            if($typeInputStoreView == 'multiselect'){
                $typeInput='multiselect';
            }
            $fieldset->addField(
                'webstore_id',
                $typeInput,
                [
                    'name' => 'webstore_id[]',
                    'label'     => __('Store View'),
                    'title'     => __('Store View'),
                    'required'  => true,
                    'style'     =>'width:50%;',
                    'size'      =>10,
                    'values'    => $store_ids_array,
                    'disabled'  => false

                ]
            );
       
        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}

