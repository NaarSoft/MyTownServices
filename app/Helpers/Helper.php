<?php

namespace App\Helpers;

use \stdClass;
use Carbon\Carbon;

class Helper
{
    public static function getESTDateFromUTC($datetime, $format ='Y-m-d')
    {
        $time = strtotime($datetime . ' UTC');

        date_default_timezone_set("America/New_York");
        return date($format, $time);
    }

    public static function getESTDateTimeFromUTC($datetime)
    {
        $time = strtotime($datetime . ' UTC');

        date_default_timezone_set("America/New_York");
        return date('Y-m-d H:i:s', $time);
    }
}