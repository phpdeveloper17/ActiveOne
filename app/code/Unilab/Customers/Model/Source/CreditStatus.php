<?php

namespace Unilab\Customers\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class PrebookStatus
 */
class CreditStatus implements OptionSourceInterface
{

    public function toOptionArray()
    {
        $opt = [];
		$opt[] = array('value' => 0,	'label' => __('Clear'));		
		$opt[] = array('value' => 1,	'label' => __('Hold'));		

        return $opt;
    }
}