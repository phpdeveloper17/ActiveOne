<?php

namespace Unilab\Benefits\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class PrebookStatus
 */
class BillingStatus implements OptionSourceInterface
{

    const BILLING_YES=1;
    const BILLING_NO=0;

    public static function getOptionArray()
    {
        return [
            self::BILLING_YES => __('Yes'),
            self::BILLING_NO => __('No')
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