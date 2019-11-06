<?php
    /**
     * Unilab_Grid Add Row Form Block.
     *
     * @category    Unilab
     *
     * @author      Unilab Software Private Limited
     */
namespace Unilab\Prescription\Block\Adminhtml\Order\View\Tab;

class Prescriptions extends \Magento\Backend\Block\Widget\Grid\Extended implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param array $data
     */

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        array $data = []
    )
    {
        $this->coreRegistry = $registry;
        $this->orderFactory = $orderFactory;
        parent::__construct($context, $backendHelper, $data);
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
    }

    public function getOrder()
    {
        return $this->coreRegistry->registry('current_order');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Prescriptions');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Prescriptions');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        // For me, I wanted this tab to always show
        // You can play around with the ACL settings 
        // to selectively show later if you want
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        // For me, I wanted this tab to always show
        // You can play around with conditions to
        // show the tab later
        return false;
    }

     /**
     * Get Tab Class
     *
     * @return string
     */
    public function getTabClass()
    {
        // I wanted mine to load via AJAX when it's selected
        // That's what this does
        return 'ajax only';
    }

    /**
     * Get Class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->getTabClass();
    }

    /**
     * Get Tab Url
     *
     * @return string
     */
    public function getTabUrl()
    {
        // customtab is a adminhtml router we're about to define
        // the full route can really be whatever you want
        return $this->getUrl('prescriptionstab/*/prescription', ['_current' => true]);
    }

    protected function _isAllowedAction($resourceId)
    {
        // return true;
        return $this->_authorization->isAllowed($resourceId);
    }


    public function getMainButtonsHtml()
    {
        $html = parent::getMainButtonsHtml();//get the parent class buttons
        
        $addButton = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(array(
            'label'     => "Add New Prescription",
            'onclick'   => "setLocation('".$this->getUrl('*/*/addprescription')."')",
            'class'     => "action-default primary add",
            'align'     => "right"
        ))->toHtml();
     
        
        return $addButton.$html;
    }
    /**
     * Prepare collection to be displayed in the grid
     *
     * @return $this
     */
    protected function _prepareCollection()
    {

        $collection = $this->getOrder()
                           ->getItemsCollection()
                           ->addFieldToFilter('prescription_id', ['neq' => 'NULL']);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return $this
     */
   
    protected function _prepareColumns()
    {
        $this->addColumn(
            'name',
            [
                'header'    => __('Product'),
                'sortable'  => true,
                'index'     => 'name'
            ]
        );
        $this->addColumn(
            'original_price',
            [
                'header'    => __('Original Price'),
                'index'     => 'original_price',
            ]
        );
        $this->addColumn(
            'price',
            [
                'header'    => __('Price'),
                'index'     => 'price',
            ]
        );
        $this->addColumn(
            'subtotal',
            [
                'header'    => __('Price'),
                'index'     => 'price',
            ]
        );
        $this->addColumn(
            'tax_amount',
            [
                'header'    => __('Tax Amount'),
                'index'     => 'tax_amount',
            ]
        );
        $this->addColumn(
            'tax_percent',
            [
                'header'    => __('Tax Percent'),
                'index'     => 'tax_percent',
            ]
        );
        $this->addColumn(
            'discount_amount',
            [
                'header'    => __('Discount Amount'),
                'index'     => 'discount_amount',
            ]
        );
        $this->addColumn(
            'row_total',
            [
                'header'    => __('Row Total'),
                'index'     => 'row_total',
            ]
        );

        $this->addColumn(
            'prescription_id',
            [
                'header'    => __('Action'),
                'index'     => 'prescription_id',
                'renderer'  => 'Unilab\Prescription\Block\Adminhtml\Grid\Column\Renderer\View'
            ]
        );


        return parent::_prepareColumns();
    }

}