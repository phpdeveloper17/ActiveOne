<?php

namespace Unilab\Inquiry\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class PrebookStatus
 */
class Type implements OptionSourceInterface
{

    const REGISTERED=1;
    const GUEST=0;

    public static function getOptionArray()
    {
        return [
            self::REGISTERED => __('REGISTERED'),
            self::GUEST => __('GUEST')
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
