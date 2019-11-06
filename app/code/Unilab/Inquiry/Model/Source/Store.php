<?php

namespace Unilab\Inquiry\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;


/**
 * Class PrebookStatus
 */
class Store implements OptionSourceInterface
{

    protected $_storeManager;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        // $res = [];
        // foreach (self::getOptionArray() as $index => $value) {
        //     $res[] = ['value' => $index, 'label' => $value];
        // }
        // return $res;

        $storeManagerDataList = $this->_storeManager->getStores();
        $options = array();

        foreach ($storeManagerDataList as $key => $value) {
            $options[] = [
                            'value' => $key,
                            'label' => $value['name']
                        ];
         }

        // echo "<pre>";
        // var_dump($options);
        // echo "</pre>";
         return $options;
    }
}
