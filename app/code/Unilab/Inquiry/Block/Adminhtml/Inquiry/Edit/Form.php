<?php
/**
 * Unilab_Grid Add New Row Form Admin Block.
 * @category    Unilab
 * @package     Unilab_Grid
 * @author      Unilab Software Private Limited
 *
 */
namespace Unilab\Inquiry\Block\Adminhtml\Inquiry\Edit;

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
        // $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        // $model = $this->_coreRegistry->registry('row_data');
        //
        //     $form = $this->_formFactory->create(
        //         ['data' => [
        //                         'id' => 'edit_form',
        //                         'enctype' => 'multipart/form-data',
        //                         'action' => $this->getData('action'),
        //                         'method' => 'post'
        //                     ]
        //         ]
        //     );
        //
        //     $form->setHtmlIdPrefix('purchase_cap_limit_');
        //     if ($model->getId()) {
        //         $fieldset = $form->addFieldset(
        //             'base_fieldset',
        //             ['legend' => __('Edit Purchase Cap Limit'), 'class' => 'fieldset-wide']
        //         );
        //         $fieldset->addField('id', 'hidden', ['name' => 'id']);
        //     } else {
        //         $fieldset = $form->addFieldset(
        //             'base_fieldset',
        //             ['legend' => __('Add Purchase Cap Limit'), 'class' => 'fieldset-wide']
        //         );
        //     }
        //
        //
        //     $fieldset->addField(
        //         'purchase_cap_id',
        //         'text',
        //         [
        //             'name' => 'purchase_cap_id',
        //             'label' => __('PCAP ID'),
        //             'id' => 'purchase_cap_id',
        //             'title' => __('PCAP ID'),
        //             'class' => 'required-entry',
        //             'required' => true,
        //         ]
        //     );
        //
        //     $fieldset->addField(
        //         'purchase_cap_des',
        //         'text',
        //         [
        //             'name' => 'purchase_cap_des',
        //             'label' => __('PCAP Name'),
        //             'id' => 'purchase_cap_des',
        //             'title' => __('PCAP Name'),
        //             'class' => 'required-entry',
        //             'required' => true,
        //         ]
        //     );
        //
        //
        //     $fieldset->addField('tnx_id', 'select', array(
        //         'label'     => __('Select Transaction Type'),
        //         'class'     => 'required-entry',
        //         'required'  => true,
        //         'name'      => 'tnx_id',
        //         'values'    => $this->getTransactiontype(),
        //     ));
        //
        //     $form->setValues($model->getData());
        //     $form->setUseContainer(true);
        //     $this->setForm($form);
        //

        return parent::_prepareForm();
    }

    public function getTransactiontype()
    {
        $connectdb = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);

        $readresult = $connectdb->query("SELECT * FROM rra_transaction_type");

        while ($items = $readresult->fetch() ) {
                $transactiontype[] = array(
                                'value'     => $items['id'],
                                'label'      => __($items['transaction_name']),
                            );      }

        return $transactiontype;
    }

}
