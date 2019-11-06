<?php
/**
 * Unilab_Grid Add New Row Form Admin Block.
 * @category    Unilab
 * @package     Unilab_Grid
 * @author      Unilab Software Private Limited
 *
 */
namespace Unilab\Benefits\Block\Adminhtml\EmployeeBenefits\Edit;

/**
 * Adminhtml Add New Row Form.
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    protected $request;
    protected $backendSession;
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
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Backend\Model\Session $backendSession,
        \Magento\Store\Model\System\Store $store,
        array $data = []
    ) {
        $this->_resourceConnection = $resourceConnection;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_eavConfig = $eavConfig;
        $this->request = $request;
        $this->_coreRegistry = $registry;
        $this->backendSession = $backendSession;
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
        $model1 = $this->_coreRegistry->registry('row_data');
        $model2 = $this->_coreRegistry->registry('employeebenefits_import');
        if($model1) 
        {
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

            if ($model1->getId()) {
                $fieldset = $form->addFieldset(
                    'base_fieldset',
                    ['legend' => __('Edit Employee Benefit'), 'class' => 'fieldset-wide']
                );
                $fieldset->addField('id', 'hidden', ['name' => 'id']);
            } else {
                $fieldset = $form->addFieldset(
                    'base_fieldset',
                    ['legend' => __('Add Employee Benefit'), 'class' => 'fieldset-wide']
                );
            }

            $fieldset->addField(
                'emp_id',
                'text',
                [
                    'name' => 'emp_id',
                    'label' => __('Employee ID'),
                    'id' => 'emp_id',
                    'title' => __('Employee ID'),
                    'class' => 'required-entry',
                    'required' => true,
                    'maxlength' => 10,
                ]
            );

            // $fieldset->addField(
            //     'emp_name',
            //     'text',
            //     [
            //         'name' => 'emp_name',
            //         'label' => __('Employee Name'),
            //         'id' => 'emp_name',
            //         'title' => __('Employee Name'),
            //         'class' => 'required-entry',
            //         'required' => true,
            //     ]
            // );

            $fieldset->addField('purchase_cap_id', 'select',   
            
                [
                    'name'  => 'purchase_cap_id',
                    'label' => __('Purchase Cap ID'),
                    'title' => __('Purchase Cap ID'),
                    'class' => 'required-entry',
                    'required' => true,
                    'values' => $this->toOptionArray()
                ]
            );    

            $fieldset->addField(
                'purchase_cap_limit',
                'text',
                [
                    'name' => 'purchase_cap_limit',
                    'label' => __('Purchase Cap Limit'),
                    'id' => 'purchase_cap_limit',
                    'title' => __('Purchase Cap Limit'),
                    'class' => 'required-entry',
                    'required' => true,
                    'maxlength' => 255,
                ]
            );

            $fieldset->addField(
                'extension',
                'text',
                [
                    'name' => 'extension',
                    'label' => __('Extension Amount'),
                    'id' => 'extension',
                    'title' => __('Extension Amount'),
                    'class' => 'required-entry',
                    'required' => true,
                    'maxlength' => 12,
                ]
            );
            if ($model1->getId()) {
                $fieldset->addField(
                    'consumed',
                    'text',
                    [
                        'name' => 'consumed',
                        'label' => __('Consumed'),
                        'id' => 'consumed',
                        'title' => __('Consumed'),
                        'class' => 'required-entry',
                        'required' => true,
                        'maxlength' => 12,
                    ]
                );
            }else{
                $fieldset->addField(
                    'consumed',
                    'text',
                    [
                        'name' => 'consumed',
                        'label' => __('Consumed'),
                        'id' => 'consumed',
                        'title' => __('Consumed'),
                        'class' => 'required-entry',
                        'required' => true,
                        'maxlength' => 12,
                        'readonly' => true,
                    ]
                );
            }

            $fieldset->addField(
                'available',
                'text',
                [
                    'name' => 'available',
                    'label' => __('Available'),
                    'id' => 'available',
                    'title' => __('Available'),
                    'class' => 'required-entry',
                    'required' => true,
                    'maxlength' => 12,
                ]
            );

            $refresh_period = $fieldset->addField('refresh_period', 'select', array(
                'label'     => __('Select Refresh Period'),
				'class'	=> 'refresh_period',
                // 'class'     => 'required-entry',
                // 'required'  => true,
                'name'      => 'refresh_period',
                'values'    =>  $this->getrefreshperiod(),
                'required' => true
            ));    

            $fieldset->addField('start_date', 'date', array(
                'name'               => 'start_date',
                'label'              => __('Start Date'),
                'after_element_html' => ' <small>Start of purchase cap limit</small>',
                'tabindex'           => 1,
                // 'image'              => $this->getSkinUrl('images/grid-cal.gif'),
                'date_format'             => $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT),
                'value'              => date($this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT,strtotime('next weekday'))),
                'required' => true,
            ));     
            
            $fieldset->addField('refresh_date', 'date', array(
                'name'               => 'refresh_date',
                'label'              => __('Refresh Date'),
                'after_element_html' => ' <small>Expiration of purchase cap limit</small>',
                'tabindex'           => 1,
                // 'image'              => $this->getSkinUrl('images/grid-cal.gif'),
                'date_format'             => $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT),
                'value'              => date( $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT,strtotime('next weekday'))),
                'required' => true,
            ));     
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
            $fieldset->addField('created_time', 'hidden', array(
                'label'     => __('Date Created'),
                'name'      => 'created_time',
            ));      

            $fieldset->addField('update_time', 'hidden', array(
                'label'     => __('Date Uploaded'),
                'name'      => 'update_time',
            ));         

            // $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

            // $fieldset->addField(
            //     'content',
            //     'editor',
            //     [
            //         'name' => 'content',
            //         'label' => __('Content'),
            //         'style' => 'height:36em;',
            //         'required' => true,
            //         'config' => $wysiwygConfig
            //     ]
            // );

            // $fieldset->addField(
            //     'publish_date',
            //     'date',
            //     [
            //         'name' => 'publish_date',
            //         'label' => __('Publish Date'),
            //         'date_format' => $dateFormat,
            //         'time_format' => 'H:mm:ss',
            //         'class' => 'validate-date validate-date-range date-range-custom_theme-from',
            //         'class' => 'required-entry',
            //         'style' => 'width:200px',
            //     ]
            // );
            // $fieldset->addField(
            //     'is_active',
            //     'select',
            //     [
            //         'name' => 'is_active',
            //         'label' => __('Status'),
            //         'id' => 'is_active',
            //         'title' => __('Status'),
            //         'values' => $this->_options->getOptionArray(),
            //         'class' => 'status',
            //         'required' => true,
            //     ]
            // );
			$refresh_period->setAfterElementHtml("
                <script type=\"text/javascript\">
                        require([
                        'jquery',
                        'mage/template',
                        'jquery/ui',
                        'mage/translate'
                    ],
                    function($, mageTemplate) {
                        $(document).ready(function(){
							var _refresh_date = $('#benefits_refresh_date').val();
                            $('#benefits_refresh_period').bind('change', function () {
                                var _refresh_period = $(this).children('option:selected').text().trim();
								if(_refresh_period == 'None'){
									$('.field-start_date').hide();
									$('#benefits_start_date').val('');
									$('#benefits_start_date').removeClass('required-entry');
									$('.field-refresh_date').hide();
									$('#benefits_refresh_date').val('');
									$('#benefits_refresh_date').removeClass('required-entry');
								}else{
									$('.field-start_date').show();
									$('#benefits_start_date').addClass('required-entry');
									$('.field-refresh_date').show();
									$('#benefits_refresh_date').addClass('required-entry');
								}
                            });
                            $('#benefits_refresh_period').trigger('change');
                        });
                    }

                );
                </script>");
            if (!$model1->getId()) {
                

                $session = $this->backendSession->getData('create_benefit');

                $model1->setData('emp_id', $session['emp_id']);
                // $model1->setData('emp_name', $session['emp_name']);
                $model1->setData('purchase_cap_id', $session['purchase_cap_id']);
                $model1->setData('purchase_cap_limit', $session['purchase_cap_limit']);
                $model1->setData('purchase_cap_limit', $session['purchase_cap_limit']);
                $model1->setData('extension', $session['extension']);
                $model1->setData('consumed', '0.00');
                $model1->setData('available', $session['available']);
                $model1->setData('refresh_period', isset($session['refresh_period']) ? $session['refresh_period'] : '142');
                $model1->setData('start_date', $session['start_date']);
                $model1->setData('refresh_date', $session['refresh_date']);
            }
            
            $form->setValues($model1->getData());
            $form->setUseContainer(true);
            $this->setForm($form);
        }
        else if($model2) {
            $form = $this->_formFactory->create(
                ['data' => [
                                'id' => 'import_form',
                                'enctype' => 'multipart/form-data',
                                'action' => $this->getData('action'),
                                'method' => 'post'
                            ]
                ]
            );

            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Manage Import'), 'class' => 'fieldset-wide']
            );

            $fieldset->addField('csv_file', 'file', array(

                'label'     => __('Browse CSV FILE'),

                'class'     => 'required-entry',

                'required'  => true,

                'name'      => 'csv_file',

            ));
			
            $form->setValues($model2->getData());
            $form->setUseContainer(true);
            $this->setForm($form);
        }

        return parent::_prepareForm();
    }

    public function getrefreshperiod()
    {
        
        $attribute_code = "refresh_period"; 
        $attribute_details = $this->_eavConfig->getAttribute("catalog_product", $attribute_code); 
        $options = $attribute_details->setStoreId(0)->getSource()->getAllOptions(); 
      
        return $options;
    }

    public function toOptionArray() 
    {
        $connectdb = $this->_resourceConnection->getConnection('core_read');

        $readresult = $connectdb->query("SELECT * FROM rra_emp_purchasecap");

        while ($items = $readresult->fetch() ) {                    
        
                $transactiontype[] = array(
                                'value'     => $items['id'],
                                'label'     => __($items['purchase_cap_des']),
                            );      
        }   
        
        return $transactiontype;
    }

}
