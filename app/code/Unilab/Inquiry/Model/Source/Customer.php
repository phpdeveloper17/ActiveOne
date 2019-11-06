<?php

namespace Unilab\Inquiry\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;


/**
 * Class PrebookStatus
 */
class Customer implements OptionSourceInterface
{

    protected $_customerFactory;
    protected $_customer;

    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Customer $customers
   )
   {
       $this->_customerFactory = $customerFactory;
       $this->_customer = $customers;
   }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $customerDataList = $this->_customer->getCollection()
               ->addAttributeToSelect("*")
               ->load();

        $options = array();

        foreach ($customerDataList as $key => $value) {
            $options[] = [
                            'value' => $value['entity_id'],
                            'label' => $value['firstname'] . " " . $value['lastname']
                        ];
         }

        // echo "<pre>";
        // var_dump($customerDataList->getData());
        // echo "</pre>";
         return $options;
    }
}
