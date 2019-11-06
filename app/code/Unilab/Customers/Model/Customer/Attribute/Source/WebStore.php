<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Customers\Model\Customer\Attribute\Source;

/**
 * Customer website attribute source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class WebStore extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_store;

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory
     * @param \Magento\Store\Model\System\Store $store
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        \Magento\Store\Model\System\Store $store
    ) {
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory);
        $this->_store = $store;
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = $this->_store->getWebsiteValuesForForm();
        }
        // echo "<pre>";
        //     print_r($this->_options);
        // echo "</pre>";
        $store_ids = $this->_store;
        $store_ids_array = array();
        // $store_ids_array = array(''=>array('label'=>'All Store Views','value'=>0));
        foreach($store_ids->toOptionArray() as $r){
            $store_ids_array[] = $r;
        }
        // echo "<pre>";
        //     print_r($store_ids_array);
        // echo "</pre>";
        // die;
        return $store_ids_array;
    }

    /**
     * @param int|string $value
     * @return string|false
     */
    public function getOptionText($value)
    {
        if (!$this->_options) {
            $this->_options = $this->getAllOptions();
        }
        foreach ($this->_options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
}
