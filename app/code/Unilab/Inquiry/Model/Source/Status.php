<?php

namespace Unilab\Inquiry\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class PrebookStatus
 */
class Status implements OptionSourceInterface
{

    const IS_READ_YES=1;
    const IS_READ_NO=0;

    public static function getOptionArray()
    {
        return [
            self::IS_READ_YES => __('Read'),
            self::IS_READ_NO => __('Pending')
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
