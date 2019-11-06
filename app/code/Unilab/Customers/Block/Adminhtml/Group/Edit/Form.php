<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Customers\Block\Adminhtml\Group\Edit;

// use Unilab\Customers\Controller\RegistryConstants;

/**
 * Adminhtml customer groups edit form
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    const GROUP_CODE_MAX_LENGTH = 255;
    /**
     * @var \Magento\Tax\Model\TaxClass\Source\Customer
     */
    protected $_taxCustomer;

    /**
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxHelper;

    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    protected $_groupRepository;

    /**
     * @var \Magento\Customer\Api\Data\GroupInterfaceFactory
     */
    protected $groupDataFactory;
    protected $_store;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Tax\Model\TaxClass\Source\Customer $taxCustomer
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param \Magento\Customer\Api\Data\GroupInterfaceFactory $groupDataFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Tax\Model\TaxClass\Source\Customer $taxCustomer,
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Customer\Api\Data\GroupInterfaceFactory $groupDataFactory,
        \Magento\Store\Model\System\Store $store,
        array $data = []
    ) {
        $this->_taxCustomer = $taxCustomer;
        $this->_taxHelper = $taxHelper;
        $this->_groupRepository = $groupRepository;
        $this->groupDataFactory = $groupDataFactory;
        $this->_store = $store;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form for render
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $groupId = $this->getRequest()->getParam('id');
        
        $model = $this->_coreRegistry->registry('customer_group_data');
        /** @var \Magento\Customer\Api\Data\GroupInterface $customerGroup */
        if ($groupId === null) {
            $customerGroup = $this->groupDataFactory->create();
            $defaultCustomerTaxClass = $this->_taxHelper->getDefaultCustomerTaxClass();
        } else {
            $customerGroup = $this->_groupRepository->getById($groupId);
            $defaultCustomerTaxClass = $customerGroup->getTaxClassId();
        }
        $store_ids = $this->_store;
        $store_ids_array = array();
        $store_ids_array = array('value'=>array('label'=>'All Store Views','value'=>0));
        foreach($store_ids->toOptionArray() as $r){
            $store_ids_array[] = $r;
        }

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Group Information')]);

        $validateClass = sprintf(
            'required-entry validate-length maximum-length-%d',
            self::GROUP_CODE_MAX_LENGTH
        );
        $name = $fieldset->addField(
            'customer_group_code',
            'text',
            [
                'name' => 'customer_group_code',
                'label' => __('Customer Group Name'),
                'title' => __('Customer Group Name'),
                'note' => __(
                    'Maximum length must be less then %1 characters.',
                    self::GROUP_CODE_MAX_LENGTH
                ),
                'class' => $validateClass,
                'required' => true,
                'maxlength' => 255
            ]
        );

        if ($customerGroup->getId() == 0 && $customerGroup->getCode()) {
            $name->setDisabled(true);
        }

        $fieldset->addField(
            'company_code',
            'text',
            [
                'name' => 'company_code',
                'label' => __('Company Code'),
                'title' => __('Company Code'),
                'id' => 'company_code',
                'required' => true,
                'maxlength' => 50
            ]
        );
        $fieldset->addField(
            'contact_number',
            'text',
            [
                'name' => 'contact_number',
                'label' => __('Contact Number'),
                'title' => __('Contact Number'),
                'id' => 'contact_number',
                'required' => false,
                'maxlength' => 255
            ]
        );
        $fieldset->addField(
            'contact_person',
            'text',
            [
                'name' => 'contact_person',
                'label' => __('Contact Person'),
                'title' => __('Contact Person'),
                'id' => 'contact_person',
                'required' => false,
                'maxlength' => 255
            ]
        );
        $fieldset->addField(
            'company_tin',
            'text',
            [
                'name' => 'company_tin',
                'label' => __('TIN'),
                'title' => __('TIN'),
                'id' => 'company_tin',
                'required' => false,
                'maxlength' => 255
            ]
        );
        $fieldset->addField(
            'company_terms',
            'text',
            [
                'name' => 'company_terms',
                'label' => __('Terms'),
                'title' => __('Terms'),
                'id' => 'company_terms',
                'note' => 'Number of Days',
                'required' => false,
                'maxlength' => 255
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
        $fieldset->addField(
            'tax_class_id',
            'select',
            [
                'name' => 'tax_class_id',
                'label' => __('Tax Class'),
                'title' => __('Tax Class'),
                'class' => '',
                'required' => false,
                'values' => $this->_taxCustomer->toOptionArray(),
            ]
        );
        $fieldset->addField(
            'credit_status',
            'select',
            [
                'name' => 'credit_status',
                'label' => __('Credit Status'),
                'title' => __('Credit Status'),
                'class' => 'required-entry',
                'required' => true,
                'values' => [0=>'Clear', 1=> 'Hold'],
            ]
        );
        $fieldset->addField(
            'is_active',
            'select',
            [
                'name' => 'is_active',
                'label' => __('Active'),
                'title' => __('Active'),
                'class' => 'required-entry',
                'required' => true,
                'values' => [0=>'No', 1=> 'Yes'],
            ]
        );
        
        if ($groupId !== null) {
            // If edit add id
            $form->addField('id', 'hidden', ['name' => 'id', 'value' => $model->getId()]);
        }

        if ($this->_backendSession->getCustomerGroupData()) {
            $form->addValues($this->_backendSession->getCustomerGroupData());
            $this->_backendSession->setCustomerGroupData(null);
        } else {
            // TODO: need to figure out how the DATA can work with forms
            $form->addValues(
                [
                    'id' => $customerGroup->getId(),
                    'customer_group_code' => $customerGroup->getCode(),
                    'tax_class_id' => $defaultCustomerTaxClass,
                ]
            );
        }
        if(!empty($groupId)){
            $form->addValues($model->getData());
        }
        
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('unilab_customers/*/save'));
        $form->setMethod('post');
        $this->setForm($form);
    }
}
