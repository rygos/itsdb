<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Routing\Route;

class MiscHelper
{
    public static function work_hours_diff($date_1,$date_2) {
        $date1 = strtotime($date_1);
        $date2 = strtotime($date_2);

        if ($date1>$date2) { $tmp=$date1; $date1=$date2; $date2=$tmp; unset($tmp); $sign=-1; } else $sign = 1;
        if ($date1==$date2) return 0;

        $days = 0;
        $working_days = array(1,2,3,4,5); // Monday-->Friday
        $working_hours = array(8, 16); // from 8:30(am) to 17:30
        $current_date = $date1;
        $beg_h = floor($working_hours[0]); $beg_m = ($working_hours[0]*60)%60;
        $end_h = floor($working_hours[1]); $end_m = ($working_hours[1]*60)%60;

        // setup the very next first working timestamp

        if (!in_array(date('w',$current_date) , $working_days)) {
            // the current day is not a working day

            // the current timestamp is set at the begining of the working day
            $current_date = mktime( $beg_h, $beg_m, 0, date('n',$current_date), date('j',$current_date), date('Y',$current_date) );
            // search for the next working day
            while ( !in_array(date('w',$current_date) , $working_days) ) {
                $current_date += 24*3600; // next day
            }
        } else {
            // check if the current timestamp is inside working hours

            $date0 = mktime( $beg_h, $beg_m, 0, date('n',$current_date), date('j',$current_date), date('Y',$current_date) );
            // it's before working hours, let's update it
            if ($current_date<$date0) $current_date = $date0;

            $date3 = mktime( $end_h, $end_m, 59, date('n',$current_date), date('j',$current_date), date('Y',$current_date) );
            if ($date3<$current_date) {
                // outch ! it's after working hours, let's find the next working day
                $current_date += 24*3600; // the day after
                // and set timestamp as the begining of the working day
                $current_date = mktime( $beg_h, $beg_m, 0, date('n',$current_date), date('j',$current_date), date('Y',$current_date) );
                while ( !in_array(date('w',$current_date) , $working_days) ) {
                    $current_date += 24*3600; // next day
                }
            }
        }

        // so, $current_date is now the first working timestamp available...

        // calculate the number of seconds from current timestamp to the end of the working day
        $date0 = mktime( $end_h, $end_m, 59, date('n',$current_date), date('j',$current_date), date('Y',$current_date) );
        $seconds = $date0-$current_date+1;

        // calculate the number of days from the current day to the end day

        $date3 = mktime( $beg_h, $beg_m, 0, date('n',$date2), date('j',$date2), date('Y',$date2) );
        while ( $current_date < $date3 ) {
            $current_date += 24*3600; // next day
            if (in_array(date('w',$current_date) , $working_days) ) $days++; // it's a working day
        }
        if ($days>0) $days--; //because we've allready count the first day (in $seconds)

        // check if end's timestamp is inside working hours
        $date0 = mktime( $beg_h, 0, 0, date('n',$date2), date('j',$date2), date('Y',$date2) );
        if ($date2<$date0) {
            // it's before, so nothing more !
        } else {
            // is it after ?
            $date3 = mktime( $end_h, $end_m, 59, date('n',$date2), date('j',$date2), date('Y',$date2) );
            if ($date2>$date3) $date2=$date3;
            // calculate the number of seconds from current timestamp to the final timestamp
            $tmp = $date2-$date0+1;
            $seconds += $tmp;
        }

        // calculate the working days in seconds

        $seconds += 3600*($working_hours[1]-$working_hours[0])*$days;

        $check1 = Carbon::parse($date_1);
        $check2 = Carbon::parse($date_2);

        if($check1->isSameDay($check2)){
            return round($sign * $seconds/3600/2,0); // to get hours
        }else{
            return round($sign * $seconds/3600,0); // to get hours
        }
        return round($sign * $seconds/3600,0); // to get hours
    }
}
