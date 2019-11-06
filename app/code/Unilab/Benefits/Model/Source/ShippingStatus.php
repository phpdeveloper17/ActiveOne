<?php

namespace Unilab\Benefits\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class PrebookStatus
 */
class ShippingStatus implements OptionSourceInterface
{

    const SHIPPING_YES=1;
    const SHIPPING_NO=0;

    public static function getOptionArray()
    {
        return [
            self::SHIPPING_YES => __('Yes'),
            self::SHIPPING_NO => __('No')
        ];
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $res = [];
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = ['value' => $index, 'label' => $value];
        }
        return $res;
    }
}