<?php

namespace Unilab\Customers\Model\Customer\Attribute\Source;

class EmploymentStatus extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{ 
    /**
     * Return array of options as value-label pairs, eg. value => label
     *
     * @return array
     */
    public function getAllOptions()
    {
        $arr = ['Resigned', 'Active'];
        $options = [];

        foreach ($arr as $key => $value) {
            $options[] = [
                'value' => $key,
                'label' => $value
            ];
        }

        $this->_options = $options;

        return $this->_options;
    }
}