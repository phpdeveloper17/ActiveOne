<?php
/**
 * Unilab_Grid Add New Row Form Admin Block.
 * @category    Unilab
 * @package     Unilab_Grid
 * @author      Unilab Software Private Limited
 *
 */
namespace Unilab\Benefits\Block\Adminhtml\TransactionType\Edit;

/**
 * Adminhtml Add New Row Form.
 */

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    protected $resourceConnection;
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
        \Magento\Framework\App\Config\ScopeConfigInterface $appConfigScopeConfigInterface,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        array $data = []
    ) {
        $this->_appConfigScopeConfigInterface = $appConfigScopeConfigInterface;
        $this->_paymentModelConfig = $paymentModelConfig;
        $this->_eavConfig = $eavConfig;
        parent::__construct($context, $registry, $formFactory, $data);
        $this->resourceConnection = $resourceConnection;
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

            $form->setHtmlIdPrefix('transaction_type_');
            if ($model->getId()) {
                $fieldset = $form->addFieldset(
                    'base_fieldset',
                    ['legend' => __('Edit Transaction Type'), 'class' => 'fieldset-wide']
                );
                $fieldset->addField('id', 'hidden', ['name' => 'id']);
            } else {
                $fieldset = $form->addFieldset(
                    'base_fieldset',
                    ['legend' => __('Add Transaction Type'), 'class' => 'fieldset-wide']
                );
            }


            $fieldset->addField(
                'transaction_name',
                'text',
                [
                    'name' => 'transaction_name',
                    'label' => __('Transaction Type Name'),
                    'id' => 'transaction_name',
                    'title' => __('Transaction Type Name'),
                    'class' => 'required-entry',
                    'required' => true,
					'maxlength' => 255
                ]
            );

            $fieldset->addField('tender_type', 'multiselect',   
            
                [
                    'name'  => 'tender_type',        
                
                    'label' => __('Tender Type'),    
                    
                    'title' => __('Tender Type'),   
                    
                    'class' => 'required-entry',             
                    
                    'required' => false,     
                    
                    'values' => $this->getTransactiontype()  
                ]
            );    

            // $fieldset->addField('tax_class', 'select', array(
            //     'label'     => __('Tax Class'),
            //     'class'     => 'required-entry',
            //     'required'  => true,
            //     'name'      => 'tax_class',
            //     'values'    => $this->getTaxClass(),
            // ));
   

            $fieldset->addField('created_time', 'hidden', array(
                'label'     => __('Date Created'),
                'name'      => 'created_time',
            ));      

            $fieldset->addField('update_time', 'hidden', array(
                'label'     => __('Date Uploaded'),
                'name'      => 'update_time',
            ));         

            $form->setValues($model->getData());
            $form->setUseContainer(true);
            $this->setForm($form);
       

        return parent::_prepareForm();
    }

    public function getTransactiontype()
    {
        $connectdb = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);

        $readresult = $connectdb->query("SELECT * FROM rra_tender_type");

        while ($items = $readresult->fetch() ) {
                $transactiontype[] = array(
                                'value'     => $items['id'],
                                'label'      => __($items['tender_name']),
                            );      }   
        
        return $transactiontype;
    }

    public function getTaxClass()
    {
        $connectdb = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);

        $readresult = $connectdb->query("SELECT * FROM tax_class WHERE class_type = 'CUSTOMER'");

        while ($items = $readresult->fetch() ) {
                $taxClass[] = array(
                                'value'     => $items['class_id'],
                                'label'      => __($items['class_name']),
                            );      }   
        
        return $taxClass;
    }

}
