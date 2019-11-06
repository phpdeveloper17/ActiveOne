<?php

namespace Unilab\Pricelist\Model\Source;


class Days implements \Magento\Framework\Option\ArrayInterface
{
    public function getAllOptions()
    {

		$days[] = array('value' => 'everyday',	'label' => __('All - Everyday'));		
		$days[] = array('value' => 'monday',	'label' => __('Monday'));		
		$days[] = array('value' => 'tuesday',	'label' => __('Tuesday'));		
		$days[] = array('value' => 'wednesday',	'label' => __('Wednesday'));		
		$days[] = array('value' => 'thursday',	'label' => __('Thursday'));		
		$days[] = array('value' => 'friday',	'label' => __('Friday'));		
		$days[] = array('value' => 'saturday',	'label' => __('Saturday'));		
		$days[] = array('value' => 'sunday',	'label' => __('Sunday'));					
					
		return $days;		

    }
	
	
	public function toOptionArray()
    {

        return $this->getAllOptions();

    }
}
