<?php

namespace spark\helpers;

/**
* Dot notation for PHP Arrays.
*
* Forked from a former micro framework, nano.
* @license http://unlicense.org/ The Unlicense
* @version 1.0
* @since 0.1
*/
class DotArray
{
    /**
     * Return a element value from a array
     *
     * @param array The array
     * @param string The key
     * @param mixed Fallback value
     */
    public static function get(array $array, $key, $fallback = null)
    {
        if (isset($array[$key])) {
            return $array[$key];
        }

        // search the array using the dot character to access nested array values
        foreach ($keys = explode('.', $key) as $key) {
            // when a key is not found or we didnt get an array to search return a fallback value
            if (!is_array($array) or !array_key_exists($key, $array)) {
                return $fallback;
            }

            $array =& $array[$key];
        }

        return $array;
    }

    /**
     * Sets a value in a array
     *
     * @param array
     * @param string
     * @param mixed
     */
    public static function set(array &$array, $key, $value)
    {
        if (isset($array[$key])) {
            $array[$key] = $value;
            return true;
        }

        $keys = explode('.', $key);

        // traverse the array into the second last key
        while (count($keys) > 1) {
            $key = array_shift($keys);

            // make sure we have a array to set our new key in
            if (!array_key_exists($key, $array) || !is_array($array[$key])) {
                $array[$key] = [];
            }

            $array =& $array[$key];
        }

        $array[array_shift($keys)] = $value;
        return true;
    }

    /**
     * Remove a key from a array
     *
     * @param array
     * @param string
     */
    public static function delete(array &$array, $key)
    {
        if (isset($array[$key])) {
            unset($array[$key]);
            return true;
        }

        $keys = explode('.', $key);

        // traverse the array into the second last key
        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (array_key_exists($key, $array)) {
                $array =& $array[$key];
            }
        }

        // if the last key exists unset it
        if (array_key_exists($key = array_shift($keys), $array)) {
            unset($array[$key]);
        }

        return true;
    }

    public static function exists(array $array, $key)
    {
        if (isset($array[$key])) {
            return true;
        }

        foreach (explode(".", $key) as $part) {
            if (!is_array($array) or !isset($array[$part])) {
                return false;
            }

            $array = $array[$part];
        }

        return true;
    }
}
