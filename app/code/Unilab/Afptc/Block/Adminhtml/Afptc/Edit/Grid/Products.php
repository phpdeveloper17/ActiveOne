<?php

namespace Unilab\Afptc\Block\Adminhtml\Afptc\Edit\Grid;


class Products extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * Contact factory
     *
     * @var ContactFactory
     */
    // protected $contactFactory;

    /**
     * @var  \Magento\Framework\Registry
     */
    protected $registry;

    protected $_objectManager = null;

    protected $_storeManager;
    protected $_productFactory;
    protected $_afptcproduct;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $registry
     * @param ContactFactory $attachmentFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Unilab\Afptc\Model\AfptcFactory $afptcFactory,
        array $data = []
    ) {
        // $this->contactFactory = $contactFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->registry = $registry;
        $this->moduleManager = $moduleManager;
        $this->_productFactory = $productFactory;
        $this->_afptcproduct = $afptcFactory;
        
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * _construct
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('productsGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('productattach_id')) {
            $this->setDefaultFilter(array('in_product' => 1));
        }
        // echo "<pre>";
        //     print_r($this->_getSelectedProducts());
        // echo "</pre>";
        // exit();
    }
    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }
    /**
     * add Column Filter To Collection
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_product') {
            $productIds = $this->_getSelectedProducts();

            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * prepare collection
     */
    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $collection = $this->_productFactory->create()->getCollection()->addAttributeToSelect(
            'sku'
        )->addAttributeToSelect(
            'name'
        )->addAttributeToSelect(
            'attribute_set_id'
        )->addAttributeToSelect(
            'type_id'
        )->setStore(
            $store
        );

        if ($this->moduleManager->isEnabled('Magento_CatalogInventory')) {
            $collection->joinField(
                'qty',
                'cataloginventory_stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left'
            );
        }
        if ($store->getId()) {
            //$collection->setStoreId($store->getId());
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                Store::DEFAULT_STORE_ID
            );
            $collection->joinAttribute(
                'custom_name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'status',
                'catalog_product/status',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'visibility',
                'catalog_product/visibility',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
        } else {
            $collection->addAttributeToSelect('price');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }

        $this->setCollection($collection);

        $this->getCollection()->addWebsiteNamesToResult();
        $this->setCollection($collection);
        // echo "<pre>";
        //     print_r($collection->getData());
        // echo "</pre>";
        // exit();
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        /* @var $model \Webspeaks\ProductsGrid\Model\Slide */
        // $model = $this->_objectManager->get('\Vendor\Productattach\Model\Productattach');

        $this->addColumn(
            'in_product',
            [
                'header_css_class' => 'a-center',
                'type' => 'radio',
                'name' => 'in_product',
                'align' => 'center',
                'index' => 'entity_id',
                'values' => $this->_getSelectedProducts(),
                'class' => 'radio',
                'renderer' => 'Unilab\Afptc\Block\Adminhtml\Afptc\Edit\Renderer\Radio',
                'html_name' => 'product_id',
                'filter_condition_callback' => array($this, 'filterChooser'),
            ]
        );

        $this->addColumn(
            'entity_id',
            [
                'header' => __('Product ID'),
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'names',
            [
                'header' => __('Name'),
                'index' => 'name',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('Sku'),
                'index' => 'sku',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'type' => 'currency',
                'index' => 'price',
                'width' => '50px',
            ]
        );
        $this->addColumn(
            'qty',
            [
                'header' => __('Qty'),
                'type' => 'number',
                'index' => 'qty',
                'width' => '50px',
            ]
        );
        $this->addColumn(
            'product_status',
            [
                'header' => __('Status'),
                'type' => 'options',
                'index' => 'status',
                'width' => '50px',
                // 'renderer' => 'Unilab\Afptc\Block\Adminhtml\Afptc\Edit\Renderer\Status',
                'options' => array(1 => 'Enabled', 0 => 'Disabled')
            ]
        );
         /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn('websites',
                array(
                    'header'=> __('Websites'),
                    'width' => '100px',
                    'sortable'  => false,
                    'index'     => 'websites',
                    'type'      => 'options',
                    'options'   => $this->_objectManager->create('Magento\Store\Model\ResourceModel\Website\Collection')->toOptionHash(),
            ));
        }
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/productsGrid', ['_current' => true]);
    }

    /**
     * @param  object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }

    protected function _getSelectedProducts()
    {
        $product_id_sel[] = $this->getAfptcProduct()->getProduct_id();
        return $product_id_sel;
    }

    /**
     * Retrieve selected products
     *
     * @return array
     */
   
    
    protected function getAfptcProduct()
    {
        $rule_id = $this->getRequest()->getParam('id');
        $afptcproduct   = $this->_afptcproduct->create();
        if ($rule_id) {
            $afptcproduct->load($rule_id);
        }
        return $afptcproduct;
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return true;
    }
}