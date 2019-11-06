<?php

namespace Unilab\Inquiry\Model;

class Status extends \Magento\Framework\DataObject
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 0;

	const CUSTOMER_GUEST    = 0;
	const CUSTOMER_LOGGED   = 1;

    static public function getOptionArray()
    {

        return  [
            [
                'value' => self::STATUS_ENABLED,
                'label' => __('Read')
            ],
            [
                'value' => self::STATUS_DISABLED,
                'label' => __('Pending')
            ]
        ];
        return array(
            self::STATUS_ENABLED    => __('Read'),
            self::STATUS_DISABLED   => __('Pending')
        );
    }


    static public function getCustomerTypes()
    {
        return array(
            self::CUSTOMER_GUEST    => __('Guest'),
            self::CUSTOMER_LOGGED   =>__('Customer')
        );
    }
}
