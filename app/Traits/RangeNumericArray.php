<?php
namespace App\Traits;

trait RangeNumericArray{
    public static function isOverlapped($periods, $start_time_key = 'from', $end_time_key = 'to')
    {
        // order periods by start_time
        usort($periods, function ($a, $b) use ($start_time_key, $end_time_key) {
            return $a[$start_time_key] <=> $b[$start_time_key];
        });
        // check two periods overlap
        foreach ($periods as $key => $period) {
           if ($key != 0) {
               if ($period[$start_time_key] < $periods[$key - 1][$end_time_key]) {
                   return true;
               }
            }
        }
        return false;
    }
}