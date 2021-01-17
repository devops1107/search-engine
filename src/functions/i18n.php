<?php

use spark\drivers\I18n\Locale;

/**
 * Translates a string
 *
 * @param string $msgid String to be translated
 * @param string $textdomain Textdomain, if left empty default will be used
 *
 * @return string translated string (or original, if not found)
 */
function __($msgid, $textdomain = null, array $variables = [])
{
    return app()->locale->gettext($msgid, $textdomain, $variables);
}

/**
 * Translates a string with escaping
 *
 * @param string $msgid String to be translated
 * @param string $textdomain Textdomain, if left empty default will be used
 *
 * @return string translated string (or original, if not found)
 */
function _e($msgid, $textdomain = null, array $variables = [])
{
    return html_escape(__($msgid, $textdomain, $variables));
}


/**
 * Register a textdomain
 *
 * @param  string $localeFile
 * @param  string $textdomain
 * @return boolean
 */
function load_textdomain($localeFile, $textdomain)
{
    return app()->locale->register($localeFile, $textdomain);
}

/**
 * Load theme text domain
 *
 * @param  string $locale
 * @param  string $textdomain
 * @return boolean
 */
function load_theme_locale($locale)
{
    $themeLocalePath = theme_locale_path($locale);
    load_textdomain($themeLocalePath, _T);
    return true;
}

/**
 * Get cookie locale
 *
 * @return string
 */
function get_cookie_locale()
{
    return get_cookie(Locale::COOKIE_NAME);
}

/**
 * Get site locale
 *
 * @return string
 */
function get_site_locale()
{
    return get_option('site_locale');
}

/**
 * Localizes numbers by using a textdomain
 *
 * @param  string $string
 * @param  mixed $textdomain
 * @return string
 */
function localize_numbers($string, $textdomain = null)
{
    $replacements = [];

    for ($i=0; $i < 10; $i++) {
        $replacements["{$i}"] = __("num_{$i}", $textdomain, ['defaultValue' => "{$i}"]);
    }

    return str_ireplace(array_keys($replacements), array_values($replacements), $string);
}

/**
 * Return different values depending on the current lanuage direction
 *
 * @param  string $forLTR Value to return for Left to right
 * @param  string $forRTL Value to return for Right to Left
 * @return string
 */
function rtl_value($forLTR, $forRTL)
{
    $dir = registry_read('locale_direction');
    if ($dir === 'rtl') {
        return $forRTL;
    }

    return $forLTR;
}

/**
 * Return different values depending on the current color scheme
 *
 * @param  string $forLight Value to return for light mode
 * @param  string $forDark Value to return for dark mode
 * @return string
 */
function darkmode_value($forLight, $forDark)
{
    $dir = registry_read('darkmode');
    if ($dir) {
        return $forDark;
    }

    return $forLight;
}
