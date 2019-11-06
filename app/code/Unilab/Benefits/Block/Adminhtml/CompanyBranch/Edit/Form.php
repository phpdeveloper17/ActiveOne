<?php
/**
 * Unilab_Grid Add New Row Form Admin Block.
 * @category    Unilab
 * @package     Unilab_Grid
 * @author      Unilab Software Private Limited
 *
 */
namespace Unilab\Benefits\Block\Adminhtml\CompanyBranch\Edit;

/**
 * Adminhtml Add New Row Form.
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
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
     * @param \Unilab\Grid\Model\Status $options,
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Unilab\Benefits\Model\Purchasecap $purchasecap_options,
        \Unilab\Benefits\Model\Source\Company $company_name_options,
        \Unilab\Benefits\Model\Source\Province $province_options,
        \Unilab\Benefits\Model\Source\ShippingStatus $shipping_options,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\System\Store $store,
        array $data = []
    ) {
        $this->_company_name_options = $company_name_options;
        $this->_purchasecap_options = $purchasecap_options;
        $this->_province_options = $province_options;
        $this->_shipping_options = $shipping_options;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_eavConfig = $eavConfig;
        $this->_store = $store;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $store_ids = $this->_store;
        $store_ids_array = array();
        $store_ids_array = array('value'=>array('label'=>'All Store Views','value'=>0));
        foreach($store_ids->toOptionArray() as $r){
            $store_ids_array[] = $r;
        }
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $model = $this->_coreRegistry->registry('row_data');
            $form = $this->_formFactory->create(
                ['data' => [
                                'id' => 'edit_form',
                                'enctype' => 'multipart/form-data',
                                'action' => $this->getData('action'),
                                'method' => 'post'
                            ]
                ]
            );

            $form->setHtmlIdPrefix('benefits_');
            if ($model->getId()) {
                $fieldset = $form->addFieldset(
                    'base_fieldset',
                    ['legend' => __('Edit Company Branch'), 'class' => 'fieldset-wide']
                );
                $fieldset->addField('id', 'hidden', ['name' => 'id']);
            } else {
                $fieldset = $form->addFieldset(
                    'base_fieldset',
                    ['legend' => __('Add Company Branch'), 'class' => 'fieldset-wide']
                );
            }

            $fieldset->addField('company_id', 'select',   
                [
                    'name'  => 'company_id',        
                    'label' => __('Company Name'),    
                    'title' => __('Company Name'),   
                    'class' => 'required-entry',             
                    'required' => false,     
                    'values' => $this->_company_name_options->toOptionArray()  
                ]
            );    


            $fieldset->addField(
                'contact_person',
                'text',
                [
                    'name' => 'contact_person',
                    'label' => __('Contact Person'),
                    'id' => 'contact_person',
                    'title' => __('Contact Person'),
                    // 'class' => 'required-entry',
                    'required' => false,
					'maxlength' => 255
                ]
            );

            $fieldset->addField(
                'contact_number',
                'text',
                [
                    'name' => 'contact_number',
                    'label' => __('Contact Number'),
                    'id' => 'contact_number',
                    'title' => __('Contact Number'),
                    // 'class' => 'required-entry',
                    'required' => false,
					'maxlength'=> 255
                ]
            );

            $fieldset->addField(
                'branch_address',
                'textarea',
                [
                    'name' => 'branch_address',
                    'label' => __('Branch Address'),
                    'id' => 'branch_address',
                    'title' => __('Branch Address'),
                    'class' => 'required-entry',
                    'required' => true,
                ]
            );

            $province = $fieldset->addField('branch_province', 'select',   
                [
                    'name'  => 'branch_province',        
                    'label' => __('State/Province'),    
                    'title' => __('State/Province'),   
                    'class' => 'required-entry',             
                    'required' => true,     
                    'values' => $this->_province_options->toOptionArray()  
                ]
            );
            $fieldset->addField('branch_city', 'select',   
                [
                    'name'  => 'branch_city',        
                    'label' => __('City'),    
                    'title' => __('City'),   
                    'class' => 'required-entry',             
                    'required' => true,     
                ]
            );
        
            $fieldset->addField(
                'branch_postcode',
                'text',
                [
                    'name' => 'branch_postcode',
                    'label' => __('Postal Code'),
                    'id' => 'branch_postcode',
                    'title' => __('Postal Code'),
                    'class' => 'required-entry',
                    'required' => true,
					'maxlength' => 11
                ]
            );

            $fieldset->addField(
                'ship_code',
                'text',
                [
                    'name' => 'ship_code',
                    'label' => __('Ship Code'),
                    'id' => 'ship_code',
                    'title' => __('Ship Code'),
                    'class' => 'required-entry',
                    'required' => true,
					'maxlength' => 255
                ]
            );

            $fieldset->addField('shipping_address', 'select',   
                [
                    'name'  => 'shipping_address',        
                    'label' => __('Is Shipping Address'),    
                    'title' => __('Is Shipping Address'),   
                    'class' => 'required-entry',             
                    'required' => true,     
                    'values' => $this->_shipping_options->toOptionArray()  
                ]
            );    

            $fieldset->addField('billing_address', 'select',   
                [
                    'name'  => 'billing_address',        
                    'label' => __('Is Billing Address'),    
                    'title' => __('Is Billing Address'),   
                    'class' => 'required-entry',             
                    'required' => true,     
                    'values' => $this->_shipping_options->toOptionArray()  
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
            $province->setAfterElementHtml("
                <script type=\"text/javascript\">
                        require([
                        'jquery',
                        'mage/template',
                        'jquery/ui',
                        'mage/translate'
                    ],
                    function($, mageTemplate) {
                        $(document).ready(function(){

                            $('#benefits_branch_province').bind('change', function () {
                                var param = 'region_id='+$(this).val()+'&branch_city=".$model->getBranch_city()."';
                                $.ajax({
                                    showLoader: true,
                                    url: '".$this->getUrl("unilab_benefits/CompanyBranchController/CityList")."',
                                    data: param,
                                    type: 'get',
                                    dataType: 'json'
                                }).done(function (data) {
                                    $('#benefits_branch_city').html(data);
                                });
                            });
                            $('#benefits_branch_province').trigger('change');
                        });
                    }

                );
                </script>");
            // $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

            $form->setValues($model->getData());
            $form->setUseContainer(true);
            $this->setForm($form);
        
       

        return parent::_prepareForm();
    }

    public function getrefreshperiod()
    {
        
        $attribute_code = "refresh_period"; 
        $attribute_details = $this->_eavConfig->getAttribute("catalog_product", $attribute_code); 
        $options = $attribute_details->getSource()->getAllOptions(false); 
      
        return $options;
    }

}
