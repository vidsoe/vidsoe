<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__current_time')){
    function __current_time($type = 'U', $offset_or_tz = ''){ // If $offset_or_tz is an empty string, the output is adjusted with the GMT offset in the WordPress option.
        if('timestamp' === $type){
            $type = 'U';
        }
        if('mysql' === $type){
            $type = 'Y-m-d H:i:s';
        }
        $timezone = $offset_or_tz ? __timezone($offset_or_tz) : wp_timezone();
        $datetime = new DateTime('now', $timezone);
        return $datetime->format($type);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__date_convert')){
    function __date_convert($string = '', $fromtz = '', $totz = '', $format = 'Y-m-d H:i:s'){
        $datetime = date_create($string, __timezone($fromtz));
        if($datetime === false){
            return gmdate($format, 0);
        }
        return $datetime->setTimezone(__timezone($totz))->format($format);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__offset_or_tz')){
    function __offset_or_tz($offset_or_tz = ''){ // Default GMT offset or timezone string. Must be either a valid offset (-12 to 14) or a valid timezone string.
        if(is_numeric($offset_or_tz)){
            return [
                'gmt_offset' => $offset_or_tz,
                'timezone_string' => '',
            ];
        }
        if(preg_match('/^UTC[+-]/', $offset_or_tz)){ // Map UTC+- timezones to gmt_offsets and set timezone_string to empty.
            return [
                'gmt_offset' => (int) preg_replace('/UTC\+?/', '', $offset_or_tz),
                'timezone_string' => '',
            ];
        }
        if(in_array($offset_or_tz, timezone_identifiers_list())){
            return [
                'gmt_offset' => 0,
                'timezone_string' => $offset_or_tz,
            ];
        }
        return [
            'gmt_offset' => 0,
            'timezone_string' => 'UTC',
        ];
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__timezone')){
    function __timezone($offset_or_tz = ''){
        return new DateTimeZone(__timezone_string($offset_or_tz));
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__timezone_string')){
    function __timezone_string($offset_or_tz = ''){
        $offset_or_tz = __offset_or_tz($offset_or_tz);
        $timezone_string = $offset_or_tz['timezone_string'];
        if($timezone_string){
            return $timezone_string;
        }
        $offset = (float) $offset_or_tz['gmt_offset'];
        $hours = (int) $offset;
        $minutes = ($offset - $hours);
        $sign = ($offset < 0) ? '-' : '+';
        $abs_hour = abs($hours);
        $abs_mins = abs($minutes * 60);
        $tz_offset = sprintf('%s%02d:%02d', $sign, $abs_hour, $abs_mins);
        return $tz_offset;
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
