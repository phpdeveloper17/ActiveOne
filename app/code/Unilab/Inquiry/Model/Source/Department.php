<?php

namespace Unilab\Inquiry\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;


/**
 * Class PrebookStatus
 */
class Department implements OptionSourceInterface
{

    protected $_helper;

    public function __construct(
        \Unilab\Inquiry\Helper\Data $helper
   )
   {
       $this->_helper = $helper;
   }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $departmentList = $this->_helper->getAllDepartments();
        $options = array();

        foreach ($departmentList as $key => $value) {
            $options[] = [
                            'value' => $value['code'],
                            'label' => $value['name']
                        ];
         }

        // echo "<pre>";
        // var_dump($departmentList);
        // echo "</pre>";
         return $options;
    }
}
