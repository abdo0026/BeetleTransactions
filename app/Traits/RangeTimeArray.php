<?php
/**
 * Created by PhpStorm.
 * User: seamlabs
 * Date: 4/26/2021
 * Time: 2:39 PM
 */

namespace App\Traits;


use Carbon\Carbon;
use DateTime;

trait RangeTimeArray
{
    public function rangeArrayBetweenTimes($start, $end, $step, $format = 'H:i') {
        $times_ranges = [];
        $end = Carbon::parse('2021-01-20 '.$end)->subSecond()->toTimeString();

        $start = strtotime($start) - strtotime('TODAY');
        $end = strtotime($end) - strtotime('TODAY');

        if($start + $step > $end)
            return [gmdate('H:i', $start)];

        foreach (range($start, $end, $step) as $increment)
        {
            $increment = gmdate('H:i', $increment);

            list($hour, $minutes) = explode(':', $increment);

            $date = new DateTime($hour . ':' . $minutes);

            $times_ranges[] = $date->format($format);
        }

        return $times_ranges;
    }

    public function getArabicWeekDay($day)
    {
        switch ($day)
        {
            case 'Sunday':
                return 'الاحد';
                break;
            case 'Monday':
                return 'الاثنين';
                break;
            case 'Tuesday':
                return 'الثلاثاء';
                break;
            case 'Wednesday':
                return 'الأربعاء';
                break;
            case 'Thursday':
                return 'الخميس';
                break;
            case 'Friday':
                return 'الجمعة';
                break;
            case 'Saturday':
                return 'السبت';
                break;
        }
    }

    public function getArabicMonth($month)
    {
        switch ($month)
        {
            case 'January':
                return 'يناير';
                break;
            case 'February':
                return 'فبراير';
                break;
            case 'March':
                return 'مارس';
                break;
            case 'April':
                return 'أبريل';
                break;
            case 'May':
                return 'مايو';
                break;
            case 'June':
                return 'يونيو';
                break;
            case 'July':
                return 'يوليو';
                break;
            case 'August':
                return 'أغسطس';
                break;
            case 'September':
                return 'سبتمبر';
                break;
            case 'October':
                return 'أكتوبر';
                break;
            case 'November':
                return 'نوفمبر';
                break;
            case 'December':
                return 'ديسمبر';
                break;
        }
    }
}
