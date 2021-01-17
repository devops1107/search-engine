<?php

namespace spark\drivers\I18n;

/**
 * Basic PHP I18n Library
 *
 * Customized version of: https://github.com/Mika-/i18next-php/
 *
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <github.com/Mika-> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return
 * ----------------------------------------------------------------------------
 */
class I18n
{
    /**
     * Translations
     *
     * @var array
     */
    protected $_translation = [];

    /**
     * Constructor
     *
     * @param string $localePath Path to the locale file
     */
    public function __construct($localePath)
    {
        // We don't throw any errors because reasons..
        if (!is_file($localePath)) {
            return false;
        }


        $this->_translation = require $localePath;

        if (!is_array($this->_translation)) {
            $this->_translation = [];
        }
    }

    /**
     * Get translation for given key
     *
     * @param string $key Key for the translation
     * @param array $variables Variables
     * @return mixed Translated string or array
     */
    public function getTranslation($key, array $variables = [])
    {
        $return = $this->_getKey($key, $variables);


        if (!$return && array_key_exists('defaultValue', $variables)) {
            $return = $variables['defaultValue'];
        }


        if ($return === false || $return === null) {
            $return = $key;
        }

        foreach ($variables as $variable => $value) {
            if (is_scalar($value)) {
                $return = str_replace("%{$variable}%", $value, $return);
            }
        }

        return $return;
    }

    /**
     * Get translation for given key
     *
     *
     * @param string $key Key for translation
     * @param array $variables Variables
     * @return mixed Translated string or array if requested. False if translation doesn't exist
     */
    protected function _getKey($key, array $variables = [])
    {
        $return = false;

        if (array_key_exists($key, $this->_translation)) {
                // Request has context
            if (array_key_exists('context', $variables)) {
                if (array_key_exists($key . '_' . $variables['context'], $this->_translation)) {
                    $key = $key . '_' . $variables['context'];
                }
            }

            if (array_key_exists('count', $variables)) {
                if ($variables['count'] != 1 && array_key_exists($key . '_plural_' . $variables['count'], $this->_translation)) {
                    $key = $key . '_plural' . $variables['count'];
                } else if ($variables['count'] != 1 && array_key_exists($key . '_plural', $this->_translation)) {
                    $key = $key . '_plural';
                }
            }

            $return = $this->_translation[$key];
        }

        return $return;
    }
}
