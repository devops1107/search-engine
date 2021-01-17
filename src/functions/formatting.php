<?php

use spark\drivers\Filter\Xss;

function sp_int_bool($input)
{
    return (int) $input === 1 ? 1 : 0;
}

function sp_int_bool_literal($input)
{
    return (int) $input === 1 ? true : false;
}

function sp_xss_filter($string)
{
    static $xss = null;

    if (!$xss) {
        $xss = new Xss;
    }

    return $xss->filter($string);
}

function sp_strip_tags($string, $remove_breaks = false)
{
    $string = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $string);
    $string = strip_tags($string);

    if ($remove_breaks) {
        $string = preg_replace('/[\r\n\t ]+/', ' ', $string);
    }

    return trim($string);
}

function sp_sort_label($key)
{
    if (isset($GLOBALS['_SPARK_I18N']['sort'][$key])) {
        return $GLOBALS['_SPARK_I18N']['sort'][$key];
    }

    $key = str_replace(['-', '_'], ' ', $key);
    return ucwords($key);
}

function sp_genders($textdomain = null)
{
    return [
        1 => __('male', _T),
        2 => __('female', _T),
        0 => __('non-binary', _T),
    ];
}

/**
 * Format valitron library's error arrays to simple string
 *
 * @param  array  $errors Errors array from valitron
 * @param  string $before string to be placed before the error message
 * @param  string $after  string to be placed after the error message
 * @return string
 */
function sp_valitron_errors(array $errors, $before = '', $after = '<br>')
{
    $formattedErrors = '';

    foreach ($errors as $key => $err) {
        $msg = implode('<br>', $err);
        $formattedErrors .= $before . $msg . $after . "\n";
    }

    return $formattedErrors;
}

function sp_valitron_plaintext(array $errors, $sep = "\n")
{
    $formattedErrors = '';

    foreach ($errors as $key => $err) {
        $msg = implode($sep, $err);
        $formattedErrors .= $msg . $sep;
    }

    $formattedErrors = trim($formattedErrors, $sep);

    return $formattedErrors;
}



/**
 *
 */
function sp_generic_ajax_format()
{
    return [
        'type' => 'info',
        'message' => false,
        'redirect' => false
    ];
}

function sp_array_wrap(array $array, $before = '', $after = '', $limit = null, $none = '', $limit_text = 'and %d other')
{
    if (empty($array)) {
        return $none;
    }

    $limit = (int) $limit;


    $html = '';

    $i = 0;
    foreach ($array as $key => $value) {
        if ($limit > 0 && $i === $limit) {
            $left = count($array) - $limit;
            if ($left > 0) {
                $html .= sprintf($limit_text, $left);
            }
            break;
        }
        $html .=  $before . $value . $after . "\n";
        $i++;
    }

    return $html;
}

/**
 * Get actual max upload size
 *
 * @return mixed
 */
function get_max_upload_size()
{
    static $max_size = - 1;

    if ($max_size < 0) {
        $post_max_size = parse_size(ini_get('post_max_size'));
        if ($post_max_size > 0) {
            $max_size = $post_max_size;
        }

        $upload_max = parse_size(ini_get('upload_max_filesize'));
        if ($upload_max > 0 && $upload_max < $max_size) {
            $max_size = $upload_max;
        }
    }
    return $max_size;
}

/**
 * Parse textual size to integer bytes
 *
 * @param  string $size Readable file size eg. 10MB
 * @return integer
 */
function parse_size($size)
{
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
    $size = preg_replace('/[^0-9\.]/', '', $size);
    if ($unit) {
        return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    }

    return round($size);
}

/**
 * Format bytes to readable file size
 *
 * @param  integer  $bytes         Bytes to parse
 * @param  string   $to            Size to convert to. available: K, M, G
 * @param  integer  $decimal_places
 * @return string
 */
function format_bytes($bytes, $to = 'M', $decimal_places = 1, $dec_separator = '.', $thousands_separator = '')
{
    $formulas = [
        'K' => number_format($bytes / 1024, $decimal_places, $dec_separator, $thousands_separator),
        'M' => number_format($bytes / 1048576, $decimal_places, $dec_separator, $thousands_separator) ,
        'G' => number_format($bytes / 1073741824, $decimal_places, $dec_separator, $thousands_separator)
    ];
    return isset($formulas[$to]) ? $formulas[$to] : 0;
}

/**
 * Format size units to readable size
 *
 * @param  integer  $bytes         Bytes to parse
 * @return string
 */
function format_size_units($bytes)
{
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}

/**
 * Returns a timezone list
 *
 * @return array
 */
function generate_timezone_list()
{
    static $regions = [
        DateTimeZone::AFRICA,
        DateTimeZone::AMERICA,
        DateTimeZone::ANTARCTICA,
        DateTimeZone::ASIA,
        DateTimeZone::ATLANTIC,
        DateTimeZone::AUSTRALIA,
        DateTimeZone::EUROPE,
        DateTimeZone::INDIAN,
        DateTimeZone::PACIFIC,
    ];

    $timezones = [];
    foreach ($regions as $region) {
        $timezones = array_merge($timezones, DateTimeZone::listIdentifiers($region));
    }

    $timezone_offsets = [];
    foreach ($timezones as $timezone) {
        $tz = new DateTimeZone($timezone);
        $timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
    }

    // sort timezone by offset
    asort($timezone_offsets);

    $timezone_list = [];
    foreach ($timezone_offsets as $timezone => $offset) {
        $offset_prefix = $offset < 0 ? '-' : '+';
        $offset_formatted = gmdate('H:i', abs($offset));

        $pretty_offset = "UTC${offset_prefix}${offset_formatted}";

        $timezone_list[$timezone] = "(${pretty_offset}) $timezone";
    }

    return $timezone_list;
}


/**
 * Get time ago value from Unix timestamp
 *
 * @param  integer  $timestamp Unix timestamp
 * @param  mixed    $textdomain The translation textdomain to apply to
 * @param  integer  $detail    Detail level
 * @return string
 */
/**
 * Get time ago value from Unix timestamp
 *
 * @param  integer  $timestamp Unix timestamp
 * @param  integer  $detail    Detail level
 * @return string
 */
function time_ago($timestamp, $textdomain = null, $detail = 1)
{
    if (!$timestamp) {
        return __('not-yet', $textdomain);
    }

    $difference = time() - $timestamp;
    $retval = '';
    $periods = [
        'decade' => 315360000,
        'year' => 31536000,
        'month' => 2628000,
        'week' => 604800,
        'day' => 86400,
        'hour' => 3600,
        'minute' => 60,
        'second'=> 1
    ];

    if ($difference < 60) {
        $retval = __('just-now', $textdomain);
        return $retval;
    } else {
        foreach ($periods as $key => $value) {
            if ($difference >= $value) {
                $time = floor($difference / $value);
                $difference %= $value;
                $retval .= ($retval ? ' ' : '') . $time . ' ';

                if ($time > 1) {
                    $retval .= __($key, $textdomain, ['count' => 2]);
                } else {
                    $retval .= __($key, $textdomain);
                }

                $detail--;
            }
            if ($detail === 0) {
                break;
            }
        }

        $retval = localize_numbers($retval, $textdomain, ['defaultValue' => $retval]);

        return sprintf(__('%s-ago', $textdomain), $retval);
    }
}

function timezone_list()
{
    static $timezones = null;

    if ($timezones === null) {
        $timezones = [];
        $offsets = [];
        $now = new DateTime('now', new DateTimeZone('UTC'));

        foreach (DateTimeZone::listIdentifiers() as $timezone) {
            $now->setTimezone(new DateTimeZone($timezone));
            $offsets[] = $offset = $now->getOffset();
            $timezones[$timezone] = '(' . format_GMT_offset($offset) . ') ' . format_timezone_name($timezone);
        }

        array_multisort($offsets, $timezones);
    }

    return $timezones;
}

function format_GMT_offset($offset)
{
    $hours = intval($offset / 3600);
    $minutes = abs(intval($offset % 3600 / 60));
    return 'GMT' . ($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '');
}

function format_timezone_name($name)
{
    $name = str_replace('/', ', ', $name);
    $name = str_replace('_', ' ', $name);
    $name = str_replace('St ', 'St. ', $name);
    return $name;
}



/**
 * Outputs the html checked attribute.
 *
 * Compares the first two arguments and if identical marks as checked
 *
 * @since 1.0.0
 *
 * @param mixed $checked One of the values to compare
 * @param mixed $current (true) The other value to compare if not just true
 * @param bool  $echo    Whether to echo or just return the string
 * @return string html attribute or empty string
 */
function checked($checked, $current = true, $echo = true)
{
    return __checked_selected_helper($checked, $current, $echo, 'checked');
}

/**
 * Outputs the html selected attribute.
 *
 * Compares the first two arguments and if identical marks as selected
 *
 * @since 1.0.0
 *
 * @param mixed $selected One of the values to compare
 * @param mixed $current  (true) The other value to compare if not just true
 * @param bool  $echo     Whether to echo or just return the string
 * @return string html attribute or empty string
 */
function selected($selected, $current = true, $echo = true)
{
    return __checked_selected_helper($selected, $current, $echo, 'selected');
}

/**
 * Outputs the html disabled attribute.
 *
 * Compares the first two arguments and if identical marks as disabled
 *
 * @since 3.0.0
 *
 * @param mixed $disabled One of the values to compare
 * @param mixed $current  (true) The other value to compare if not just true
 * @param bool  $echo     Whether to echo or just return the string
 * @return string html attribute or empty string
 */
function disabled($disabled, $current = true, $echo = true)
{
    return __checked_selected_helper($disabled, $current, $echo, 'disabled');
}

/**
 * Outputs the html readonly attribute.
 *
 * Compares the first two arguments and if identical marks as readonly
 *
 * @since 4.9.0
 *
 * @param mixed $readonly One of the values to compare
 * @param mixed $current  (true) The other value to compare if not just true
 * @param bool  $echo     Whether to echo or just return the string
 * @return string html attribute or empty string
 */
function readonly($readonly, $current = true, $echo = true)
{
    return __checked_selected_helper($readonly, $current, $echo, 'readonly');
}

/**
 * Private helper function for checked, selected, disabled and readonly.
 *
 * Compares the first two arguments and if identical marks as $type
 *
 * @since 2.8.0
 * @access private
 *
 * @param mixed  $helper  One of the values to compare
 * @param mixed  $current (true) The other value to compare if not just true
 * @param bool   $echo    Whether to echo or just return the string
 * @param string $type    The type of checked|selected|disabled|readonly we are doing
 * @return string html attribute or empty string
 */
function __checked_selected_helper($helper, $current, $echo, $type)
{
    if ((string) $helper === (string) $current) {
        $result = " $type='$type'";
    } else {
        $result = '';
    }

    if ($echo) {
        echo $result;
    }

    return $result;
}
