<?php
/**
 * Unilab_Grid Add New Row Form Admin Block.
 * @category    Unilab
 * @package     Unilab_Grid
 * @author      Unilab Software Private Limited
 *
 */
namespace Unilab\Benefits\Block\Adminhtml\TenderType\Edit;

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
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
     * @param \Unilab\Grid\Model\Status $options,
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Payment\Model\Config $paymentModelConfig,
        \Magento\Shipping\Model\Config $shippingConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $appConfigScopeConfigInterface,
        array $data = []
    ) {
        $this->_appConfigScopeConfigInterface = $appConfigScopeConfigInterface;
        $this->_paymentModelConfig = $paymentModelConfig;
        $this->_shippingConfig = $shippingConfig;
        $this->_eavConfig = $eavConfig;
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
                ['data' => [
                                'id' => 'edit_form',
                                'enctype' => 'multipart/form-data',
                                'action' => $this->getData('action'),
                                'method' => 'post'
                            ]
                ]
            );

            $form->setHtmlIdPrefix('tender_type_');
            if ($model->getId()) {
                $fieldset = $form->addFieldset(
                    'base_fieldset',
                    ['legend' => __('Edit Tender Type'), 'class' => 'fieldset-wide']
                );
                $fieldset->addField('id', 'hidden', ['name' => 'id']);
            } else {
                $fieldset = $form->addFieldset(
                    'base_fieldset',
                    ['legend' => __('Add Tender Type'), 'class' => 'fieldset-wide']
                );
            }


            $fieldset->addField(
                'tender_name',
                'text',
                [
                    'name' => 'tender_name',
                    'label' => __('Tender Type Name'),
                    'id' => 'tender_name',
                    'title' => __('Tender Type Name'),
                    'class' => 'required-entry',
                    'required' => true,
					'maxlength' => 255
                ]
            );

            $fieldset->addField('payment_method_code', 'select',   
            
                [
                    'name'  => 'payment_method_code',        
                
                    'label' => __('Select Paymethod Method'),    
                    
                    'title' => __('Select Paymethod Method'),   
                    
                    'class' => 'required-entry',             
                    
                    'required' => false,     
                    
                    'values' => $this->toPaymentOptionArray()  
                ]
            );    
            $fieldset->addField('shipping_method', 'select',   
            
            [
                'name'  => 'shipping_method',        
            
                'label' => __('Select Shipping Method'),    
                
                'title' => __('Select Shipping Method'),   
                
                'class' => 'required-entry',             
                
                'required' => false,     
                
                'values' => $this->toShippingOptionArray()  
            ]
        ); 
       

            $form->setValues($model->getData());
            $form->setUseContainer(true);
            $this->setForm($form);
       

        return parent::_prepareForm();
    }

    public function toPaymentOptionArray()
    {
        $payments = $this->_paymentModelConfig->getActiveMethods();
        $methods = array();
        foreach ($payments as $paymentCode => $paymentModel) {
            $paymentTitle = $this->_appConfigScopeConfigInterface
                ->getValue('payment/'.$paymentCode.'/title');
            $methods[$paymentCode] = array(
                'label' => $paymentTitle,
                'value' => $paymentCode
            );
        }
        return $methods;
    }

    public function toShippingOptionArray()
    {
        $shipping =  $this->_shippingConfig->getActiveCarriers();
        $methods = array();
 
        foreach ($shipping as $shippingCode => $shippingModel) {
            $shippingTitle = $this->_appConfigScopeConfigInterface
                ->getValue('carriers/' . $shippingCode . '/title');

            $methods[$shippingCode] = array(
                'label' => $shippingTitle,
                'value' => $shippingCode
            );
        }
        return $methods;
    }

}
