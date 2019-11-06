<?php

namespace Unilab\Benefits\Model\Source;

class Visibility implements \Magento\Framework\Option\ArrayInterface
{
	public function toOptionArray()
    {
		$opt = [];
		$opt[] = array('value' => '1',	'label' => __('Not Visible Individually'));		
		$opt[] = array('value' => '2',	'label' => __('Catalog'));		
		$opt[] = array('value' => '3',	'label' => __('Search'));		
		$opt[] = array('value' => '4',	'label' => __('Catalog, Search'));		
					
		return $opt;
    }
}
