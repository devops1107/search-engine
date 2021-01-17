<?php

use Slim\Slim;
use Valitron\Validator;
use spark\drivers\Auth\GoogleReCaptcha;
use spark\drivers\I18n\Locale;
use spark\models\RoleModel;
use spark\models\UserModel;

/*--------------------------------------------------------------------
 * System Helper Functions
 *
 * Mostly those that are loaded as a singleton within the app.
 * For other models, a static instance will be created and future calls
 * will be served from there.
 *
 * No further comments will be provided for this helper functions.
 * Instead their respective file name will be provided.
 *
 *--------------------------------------------------------------------
 */

function sp_register_psr4($prefix, $paths, $prepend = false)
{
    return app()->composer->addPsr4($prefix, $paths);
}

// src/drivers/Auth/CurrentUser.php

function is_logged()
{
    return app()->user->isLogged();
}

function current_user_can($permission)
{
    return app()->user->hasPermission($permission);
}

function get_logged_user()
{
    return app()->user;
}

function current_user_ID()
{
    return get_logged_user()->getID();
}

function current_user_field($field, $fallback = null)
{
    return get_logged_user()->getField($field, $fallback);
}

function current_user_meta($field, $fallback = null)
{
    return get_logged_user()->getMeta($field, $fallback);
}

function current_user_avatar_uri($resolution = 64)
{
    $email = current_user_field('email', 'john@example.com');
    $avatar = current_user_field('avatar');
    return sp_user_avatar_uri($avatar, $email, $resolution);
}

function current_user_cover_uri($resolution = null)
{
    return sp_user_cover_uri(current_user_field('cover'), $resolution);
}

function username_exists($username, $except = null)
{
    $userModel = new UserModel;

    $filters['where'][] = ['username', '=', $username];

    if ($except) {
        $filters['where'][] = ['username', '!=', $except];
    }

    return (bool) $userModel->countRows(null, $filters);
}

function email_exists($email, $except = null)
{
    $userModel = new UserModel;

    $filters['where'][] = ['email', '=', $email];

    if ($except) {
        $filters['where'][] = ['email', '!=', $except];
    }

    return (bool) $userModel->countRows(null, $filters);
}



// src/models/OptionModel.php
function get_option($name, $fallback = null)
{
    return app()->options->get($name, $fallback);
}

function get_option_json($name, $fallback = [])
{
    return app()->options->getJsonValue($name, $fallback);
}

function set_option($name, $value, $autoload = null)
{
    return app()->options->set($name, $value, $autoload);
}

function get_loaded_options()
{
    return app()->options->getAll();
}

// src/helpers/Session.php
function session_set($key, $value)
{
    return app()->session->set($key, $value);
}

function session_get($key, $default = null)
{
    return app()->session->get($key, $default);
}

function session_delete($key)
{
    return app()->session->delete($key);
}

function session_clear()
{
    return app()->session->clear();
}

function session_exists($key)
{
    return app()->session->exists($key);
}

function session_flush()
{
    return app()->session->destroy();
}

function session_identifier($new = false)
{
    return app()->session->id($new);
}

// src/drivers/Nav/BreadCrumbs.php
function breadcrumb_add($id, $label, $url = null)
{
    return app()->breadcrumbs->add($id, $label, $url);
}

function breadcrumb_remove($id)
{
    return app()->breadcrumbs->remove($id);
}

function breadcrumb_render($before = '', $after = '', array $attrs = [])
{
    return app()->breadcrumbs->renderHtml($before, $after, $attrs);
}

function breadcrumb_render_json($scriptTags = true)
{
    return app()->breadcrumbs->renderJson($scriptTags);
}

// src/helpers/Registry.php
function registry_store($key, $value, $readOnly = false)
{
    return app()->registry->store($key, $value, $readOnly);
}

function registry_increment($key)
{
    $value = (int) registry_read($key, 0);
    return registry_store($key, $value + 1);
}

function registry_decrement($key)
{
    $value = (int) registry_read($key, 0);
    return registry_store($key, $value - 1);
}

function registry_read($key, $default = null)
{
    return app()->registry->read($key, $default);
}

function registry_prop($name, $key, $default = null)
{
    return app()->registry->prop($name, $key, $default);
}

function registry_delete($key)
{
    return app()->registry->delete($key);
}

function registry_has($key)
{
    return app()->registry->has($key);
}

/**
 * Access the current theme path
 *
 * @param  string $path
 * @return
 */
function current_theme_path($path = '')
{
    $currentTheme = get_option('active_theme');
    return basepath($path, trailingslashit(BASEPATH) . trailingslashit(THEME_DIR) . trailingslashit($currentTheme));
}

/**
 * Path to the theme locale folder
 *
 * @param  string $locale
 * @return string
 */
function theme_locale_path($locale)
{
    return current_theme_path(Locale::THEME_LOCALE_DIR . "/{$locale}/{$locale}.php");
}

function theme_locale_dir()
{
    return current_theme_path(Locale::THEME_LOCALE_DIR);
}

/**
 * Path to the dashboard locale folder
 *
 * @param  string $locale
 * @return string
 */
function dashboard_locale_path($locale)
{
    return srcpath("locales/{$locale}/{$locale}.php");
}


/**
 * Store post data as slim flash
 *
 * @param  array  $post list of data
 * @return
 */
function sp_store_post(array $post)
{
    $app = Slim::getInstance();
    foreach ($post as $key => $value) {
        $app->flash("__spark_post__{$key}", $value);
    }
}

/**
 * Access data stored via sp_store_post
 *
 * @param  string $key
 * @param  mixed $fallback
 * @param  string $escaper
 * @return mixed
 */
function sp_post($key, $fallback = null, $escaper = 'e_attr')
{
    $flash = app()->environment['slim.flash'];
    $value = $fallback;
    if (isset($flash["__spark_post__{$key}"])) {
        $value = $flash["__spark_post__{$key}"];
    }

    if (!$escaper) {
        return $value;
    }

    return $escaper($value);
}

/**
 * Get the user avatar url
 *
 * @param  string  $avatarFieldValue
 * @param  string  $email
 * @param  integer $resolution
 * @return string
 */
function sp_user_avatar_uri($avatarFieldValue, $email, $resolution = 32)
{
    if (empty($avatarFieldValue)) {
        return sp_gravatar_uri($email, $resolution);
    }

    // could be an abs url
    if (filter_var($avatarFieldValue, FILTER_VALIDATE_URL)) {
        return $avatarFieldValue;
    }

    if (!$resolution) {
        return base_uri($avatarFieldValue);
    }

    return sp_thumbnail_uri($avatarFieldValue, "{$resolution}x{$resolution}");
}

/**
 * Get the user cover url
 *
 * @param  string $coverFieldValue
 * @param  string $resolution
 * @return string
 */
function sp_user_cover_uri($coverFieldValue, $resolution = null)
{
    if (empty($coverFieldValue)) {
        return current_theme_uri('assets/img/default-cover.jpg');
    }

    if (!$resolution) {
        return ensure_abs_url($coverFieldValue);
    }

    return sp_thumbnail_uri($coverFieldValue, $resolution);
}

/**
 * Get gravatar URL from an email
 *
 * @param  string  $email
 * @param  integer $resolution
 * @return string
 */
function sp_gravatar_uri($email, $resolution = 32)
{
    $resolution = (int) $resolution;
    return 'https://www.gravatar.com/avatar/' . md5($email) . '?s=' . $resolution;
}

/**
 * Check if an user is online by their last activity timestamp
 *
 * @param  integer $timestamp
 * @return boolean
 */
function sp_is_online($timestamp)
{
    $span = (int) app()->config('site.user_online_lifespan');
    return $span > time() - $timestamp;
}

/**
 * Get thumbnail URL for internal image
 *
 * @param  string $path
 * @param  string $size
 * @return string
 */
function sp_thumbnail_uri($path, $size = '150x150')
{
    static $base = false;

    if (!$base) {
        $base = base_uri('thumb.php');
    }

    return $base . "?src={$path}&size={$size}";
}

/**
 * Get dashboard locales list
 *
 * @return array
 */
function sp_dashboard_locales()
{
    return app()->locale->getDashboardLanguages();
}

/**
 * Get the site logo URL
 *
 * @return string
 */
function sp_logo_uri($logo = null)
{
    return ensure_abs_url(get_option('site_logo'));
}

/**
 * Ensures absolute URL
 *
 * @param  string $urlOrPath
 * @return string
 */
function ensure_abs_url($urlOrPath)
{
    // If starts with hash identifier or javascript return original string
    if (mb_strpos($urlOrPath, '#') === 0 || mb_strpos($urlOrPath, 'javascript:') === 0) {
        return $urlOrPath;
    }

    // If it's without any protocol, append http to start
    if (mb_strpos($urlOrPath, '//') === 0) {
        return 'http' . $urlOrPath;
    }

    // If starts with http:// or https:// it's a URL
    // no further check because, performance
    // no regex because, performance
    // no filter_var because unicode
    if (mb_strpos($urlOrPath, 'http://') === 0 || mb_strpos($urlOrPath, 'https://') === 0) {
        return $urlOrPath;
    }

    return base_uri($urlOrPath);
}
/**
 * Verify recaptcha
 *
 * @param  boolean $force
 * @return boolean
 */
function sp_verify_recaptcha($page = '', $force = false)
{
    if (current_user_can('bypass_captcha')) {
        return true;
    }

    static $captchaLocations = [];

    if (empty($captchaLocations)) {
        $captchaLocations = get_option_json('captcha_locations');
    }

    $enabled = (int) get_option('captcha_enabled');

    if ($force) {
        $enabled = true;
    }

    // Captcha exceptions
    if (!empty($page) && !$force) {
        if (!empty($captchaLocations[$page])) {
            return true;
        }
    }

    if ($enabled) {
        $userInput = app()->request->post('g-recaptcha-response');
        $captcha = new GoogleReCaptcha;
        return $captcha->verify($userInput);
    }

    return true;
}

/**
 * Returns whether we are in frontend or not
 *
 * @return boolean
 */
function is_frontend()
{
    return registry_read('is_frontend', false);
}

/**
 * Returns whether we are in dashboard or not
 *
 * @return boolean
 */
function is_dashboard()
{
    return registry_read('is_dashboard', false);
}

/**
 * Returns the logger object
 *
 * @return object
 */
function logger()
{
    return app()->getLog();
}

/**
 * Returns whether the current user is admin or not
 *
 * @return boolean
 */
function is_admin()
{
    return current_user_field('role_id') === RoleModel::TYPE_ADMIN;
}

/**
 * Returns whether the current user is moderator or not
 *
 * @return boolean
 */
function is_moderator()
{
    return current_user_field('role_id') === RoleModel::TYPE_MOD;
}

function str_locale_handle($string)
{
    $string = trim($string);
    return str_replace(' ', '-', mb_strtolower($string));
}

/**
 * Middleware callback for csrfguard, should be set to POST routes
 *
 * @param  object $route
 */
function csrf_guard($route)
{
    if (!config('enable_csrfguard', true)) {
        return;
    }

    $app = app();

    $key = config('csrf_key', 'csrf_token');

    $userToken = $app->request->isPost() ? $app->request->post($key) : $app->request->get($post);
    $sessionToken = session_get($key);

    if (empty($userToken) || $userToken !== $sessionToken) {
        return $app->halt(500, 'Invalid CSRF Token');
    }
}

/**
 * Middleware callback for honeypot, should be set to POST routes
 *
 * @param  object $route
 */
function honeypot($route)
{
    if (!config('enable_csrfguard', true)) {
        return;
    }

    $app = app();
    $key = config('honeypot_key', '__required_for_safety__');
    $input = $app->request->isPost() ? $app->request->post($key) : $app->request->get($post);

    if (!empty($input)) {
        return $app->halt(500, 'Bruh! you just fell into the honeypot!');
    }
}
