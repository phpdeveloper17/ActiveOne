<?php
/**
 * Unilab_Banners Add New Row Form Admin Block.
 * @category    Unilab
 * @package     Unilab_Banners
 * @author      Unilab Software Private Limited
 *
 */
namespace Unilab\Prescription\Block\Adminhtml\Prescription\Edit;

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
     * @param \Unilab\Banners\Model\Status $options,
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Unilab\Prescription\Model\Status $options,
        array $data = []
    ) {
        $this->_options = $options;
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
        $model = $this->_coreRegistry->registry('prescription');
        $form = $this->_formFactory->create(
            ['data' => 
                [
                    'id' => 'edit_form',
                    'enctype' => 'multipart/form-data',
                    'action' => $this->getData('action'),
                    'method' => 'post'
                ]
            ]
        );

        // $form->setHtmlIdPrefix('w_');
        if ($model->getPrescriptionId()) {

           
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['class' => 'fieldset-wide']
            );

            $fieldset->addField('prescription_id', 'hidden', ['name' => 'prescription_id']);
  
            $fieldset->addField(
                'date_prescribed',
                'date',
                [
                    'name' => 'date_prescribed',
                    'label' => __('Date Prescribed'),
                    'required' => false,
                    'singleClick'=> true,
                    'date_format' => $dateFormat,
                    'input_format' => 'yy-mm-dd',
                    'class' => 'date-picker'
                ]
            );

            $fieldset->addField(
                'patient_name',
                'text',
                [
                    'name' => 'patient_name',
                    'label' => __('Patient\'s Name'),
                    'id' => 'patient_name',
                    'title' => __('Patient\'s Name'),
                    'class' => 'required-entry',
                    'required' => false,
                ]
            );

            $fieldset->addField(
                'ptr_no',
                'text',
                [
                    'name' => 'ptr_no',
                    'label' => __('PTR No.'),
                    'id' => 'ptr_no',
                    'title' => __('StaPTR No.tus'),
                    'required' => false,
                ]
            );

            $fieldset->addField(
                'doctor',
                'text',
                [
                    'name' => 'doctor',
                    'label' => __('Doctor\'s Name'),
                    'id' => 'doctor',
                    'title' => __('Doctor\'s Name'),
                    'required' => false,
                ]
            );

            $fieldset->addField(
                'clinic',
                'text',
                [
                    'name' => 'clinic',
                    'label' => __('Clinic Name'),
                    'id' => 'clinic',
                    'title' => __('Clinic Name'),
                    'required' => false,
                ]
            );

            $fieldset->addField(
                'clinic_address',
                'textarea',
                [
                    'name' => 'clinic_address',
                    'label' => __('Clinic Address'),
                    'id' => 'clinic_address',
                    'title' => __('Clinic Address'),
                    'required' => false,
                ]
            );

            $fieldset->addField(
                'contact_number',
                'text',
                [
                    'name' => 'contact_number',
                    'label' => __('Contact Number'),
                    'id' => 'clinic',
                    'title' => __('Contact Number'),
                    'required' => false,
                ]
            );


            $fieldset->addField(
                'expiry_date',
                'date',
                [
                    'name' => 'expiry_date',
                    'label' => __('Expiry Date'),
                    'required' => false,
                    'singleClick'=> true,
                    'date_format' => $dateFormat,
                    'id' => 'expiry_date',
                    'class' => 'date-picker'
                ]
            )->setAfterElementHtml(
                '<script>
                    require(
                        [
                            "jquery",
                            "mage/calendar"
                        ], 
                        function($){
                            $(".date-picker").datepicker({
                                prevText: "&#x3c;zurück", prevStatus: "",
                                prevJumpText: "&#x3c;&#x3c;", prevJumpStatus: "",
                                nextText: "Vor&#x3e;", nextStatus: "",
                                nextJumpText: "&#x3e;&#x3e;", nextJumpStatus: "",
                                monthNames: ["January","February","March","April","May","June",
                                "July","August","September","October","November","December"],
                                monthNamesShort: ["Jan","Feb","Mar","Apr","May","Jun",
                                "Jul","Aug","Sep","Oct","Nov","Dec"],
                                dayNames: ["Sunday ","Monday","Tuesday ","Wednesday","Thursday","Friday","Saturday"],
                                dayNamesShort: ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],
                                dayNamesMin: ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],
                                showMonthAfterYear: false,
                                dateFormat:"d/m/yy"
                            });
                        });
                </script>'
            );

            $fieldset->addField(
                'remarks',
                'textarea',
                [
                    'name' => 'remarks',
                    'label' => __('Remarks'),
                    'id' => 'remarks',
                    'title' => __('Remarks'),
                    'required' => false,
                ]
            );

            $fieldset->addField(
                'consumed',
                'checkbox',
                [
                    'name' => 'consumed',
                    'label' => __('Consumed'),
                    'required' => false,
                    'value' => 1,
                    'checked' => $model->getConsumed() ? true : false
                ]
            );

            $fieldset->addField(
                'status',
                'select',
                [
                    'name' => 'status',
                    'label' => __('Status'),
                    'id' => 'status',
                    'title' => __('Status'),
                    'values' => $this->_options->getOptionArray(),
                    'class' => 'status',
                    'required' => false,
                ]
            );

            $form->setValues($model->getData());
            
            if($model->getBannerId()){
                $p = $form->getElement('original_filename')->getValue();
                $form->getElement('original_filename')->setValue('banners/' . $p);
            }
            
            $form->setUseContainer(true);
            $this->setForm($form);

            return parent::_prepareForm();
        } 
    }
}
