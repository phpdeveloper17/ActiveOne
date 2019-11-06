<?php
namespace Unilab\Inquiry\Model\System\Config\Source\Cms;

class Blocks implements \Magento\Framework\Data\OptionSourceInterface
{
    protected $_options;

    protected $_cmsModel;

    protected $_storeManager;

    public function __construct(
         \Magento\Cms\Model\Block $blockModel,
         \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_cmsModel = $blockModel;
        $this->_storeManager = $storeManager;
    }

    public function toOptionArray($isMultiselect=false)
    {
        if (!$this->_options) {
            $collection = $this->_cmsModel->getCollection();
            $collection->addStoreFilter($this->_storeManager->getStore()->getId());
            $collection->addFieldToFilter('is_active', array('eq' => 1));
            $this->_options = $collection->loadData()->toOptionArray(false);
        }

        $options = $this->_options;
        if(!$isMultiselect){
            array_unshift($options, array('value'=>'', 'label'=> __('--Please Select--')));
        }

        return $options;
    }
}
