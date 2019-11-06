<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Locale timezone source
 */
namespace Unilab\Pricelist\Model\Source;

/**
 * @api
 * @since 100.0.2
 */
class TimeFormatAmPm implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];
        // return [date("g:i a", strtotime("13:30:30 UTC"))=>date("g:i a", strtotime("13:30:30 UTC"))];
        $hours = 13 - 1; //amount of hours working in day
        for($i = 0; $i < $hours*2; $i++){
            $minutes_to_add = 3600 * $i; // add 1 hr.
            $timeslot = date('h:i a', strtotime('1:00:00')+$minutes_to_add);
            $timeslotkey = date('G:i:s', strtotime('1:00:00')+$minutes_to_add);
            if($timeslotkey == '12:00:00'){
                // $result['00:00:00'] = '12:00 AM';
                $result[] = array('value' => '00:00:00',	'label' => __('12:00 AM'));	
                continue;
            }
            if($timeslotkey == '0:00:00'){
                // $result['23:59:00'] = '11:59 PM';
                $result[] = array('value' => '23:59:00',	'label' => __('11:59 PM'));	
                continue;
            }
            $result[] = array('value' => strtoupper($timeslotkey),	'label' => __(strtoupper($timeslot)));	
            // $result[strtoupper($timeslotkey)] = strtoupper($timeslot);
        }
       return $result;
    }
    // public function getAllOptions()
    // {

	// 	$days[] = array('value' => 'everyday',	'label' => __('All - Everyday'));		
	// 	$days[] = array('value' => 'monday',	'label' => __('Monday'));		
	// 	$days[] = array('value' => 'tuesday',	'label' => __('Tuesday'));		
	// 	$days[] = array('value' => 'wednesday',	'label' => __('Wednesday'));		
	// 	$days[] = array('value' => 'thurday',	'label' => __('Thurday'));		
	// 	$days[] = array('value' => 'friday',	'label' => __('Friday'));		
	// 	$days[] = array('value' => 'saturday',	'label' => __('Saturday'));		
	// 	$days[] = array('value' => 'sunday',	'label' => __('Sunday'));					
					
	// 	return $days;		

    // }
	
	
	// public function toOptionArray()
    // {

    //     return $this->getAllOptions();

    // }
}
