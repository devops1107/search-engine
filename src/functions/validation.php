<?php

use Valitron\Validator;
use spark\drivers\Auth\ReservedUsernames;
use spark\models\Model;
use spark\models\UserModel;

// Add is_username to validator
Validator::addRule(
    'username',
    function ($field, $value, array $params, array $fields) {
        return is_username($value);
    },
    sprintf(__("{field} can contain A-z0-9_ only, between %d and %d characters"), config('internal.username_minlength'), config('internal.username_maxlength'))
);

// To Ensure unique E-mail
Validator::addRule(
    'uniqueEmail',
    function ($field, $value, array $params, array $fields) {
        $except = isset($params[0]) ? $params[0] : null;
        return !email_exists($value, $except);
    },
    __("{field} already exists in database")
);

// To Ensure unique username
Validator::addRule(
    'uniqueUsername',
    function ($field, $value, array $params, array $fields) {
        if (ReservedUsernames::isReserved($value)) {
            return false;
        }

        $except = isset($params[0]) ? $params[0] : null;
        return !username_exists($value, $except);
    },
    __("{field} already exists in database")
);

// Add is_valid_timezone to validator
Validator::addRule(
    'timezone',
    function ($field, $value, array $params, array $fields) {
        return is_valid_timezone($value);
    },
    __("{field} must be a valid PHP timezone identifier")
);

/**
 * Returns if a string is valid username or not
 *
 * @param  string  $input
 * @return boolean
 */
function is_username($input)
{
    return (bool) preg_match("/^[A-Za-z][A-Za-z_0-9]+$/", $input);
}

/**
 * Returns if timezone string is PHP valid or not
 *
 * @param  string  $timezone The input
 * @return boolean
 */
function is_valid_timezone($timezone)
{
    static $list;

    if (!$list) {
        $list = timezone_identifiers_list();
    }

    return in_array($timezone, $list);
}


/**
 * Check if a filename is dotfile or not
 *
 * @param  string  $fileName Input string
 * @return boolean
 */
function is_dotfile($fileName)
{
    return (bool) preg_match('%(?:^|[\\\\\/])(\.(?!\.)[^\\\\\/]+)$%', $fileName);
}

/**
 * Ensure unique value for a column by appending numerals with a hyphen with it
 * Mostly used for URL slugs
 *
 * @param  Model  $model The model instance
 * @param  string $column The table's column name
 * @param  string $value Current value
 * @param  string $existingValue Existing value, helpful while performing update and the slug remains the same
 * @return string
 */
function ensure_unique_value(Model $model, $column, $value, $existingValue = false, $sep = '-')
{
    // If the value is same return
    if ($value === $existingValue) {
        return $value;
    }

    $originalValue = $value;
    $valueName = $originalValue;

    $i = 1;
    while ($model->exists($column, $valueName)) {
        $i++;
        $valueName = $originalValue . "{$sep}{$i}";
    }

    return $valueName;
}
