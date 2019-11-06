<?php

namespace Unilab\Customers\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class PrebookStatus
 */
class isActive implements OptionSourceInterface
{

    const IS_ACTIVE_YES = 1;
    const IS_ACTIVE_NO = 0;

    public static function getOptionArray()
    {
        return [
            self::IS_ACTIVE_YES => __('Yes'),
            self::IS_ACTIVE_NO => __('No')
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