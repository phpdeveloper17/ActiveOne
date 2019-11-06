<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Locale timezone source
 */
namespace Unilab\Afptc\Model\Source;

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
            $timeslotkey = date('G:i:s a', strtotime('1:00:00')+$minutes_to_add);
            if($timeslotkey == '12:00:00 pm'){
                $result['00:00:00 AM'] = '12:00 AM';
                continue;
            }
            if($timeslotkey == '0:00:00 am'){
                $result['23:59:00 PM'] = '11:59 PM';
                continue;
            }
            $result[strtoupper($timeslotkey)] = strtoupper($timeslot);
        }
       return $result;
    }
}
