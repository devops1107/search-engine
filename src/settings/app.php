<?php

return [
    /**
     * Application Mode
     *
     * @var string
     */
    'mode' => is_dev() ? 'development' : 'production',

    /**
     * Set Base URI of the App
     *
     * Leave empty to be detected automatically
     *
     * @var string|boolean
     */
    'uri' => false,

    /**
     * Toggle debug mode
     *
     * @var boolean
     */
    'debug' => is_dev(),

    /**
     * PHP Charset
     *
     * @var string
     */
    'charset' => 'UTF-8',

    /**
     * PHP Timezone
     *
     * @var string
     */
    'timezone' => 'Asia/Dhaka',

    /**
     * Cookies Options
     *
     */
    'cookies.path' => '/',
    'cookies.domain' => detect_cookie_domain(),
    'cookies.secure' => false,
    'cookies.httponly' => false,

    /**
     * Trusted domain names. Best not to mess with this setting if you don't know what you're doing
     *
     * Array of external domain names to trust when checking for redirect or referrer
     *
     * NOTE: You're app's base URL and current URL's domain will be added automatically to this array
     *       No need to add them manually. Add only domain name, all sub-domains are allowed automatically
     */
    'trusted_domains' => [
        'google.com'
    ],


    /**
     * Session Options
     *
     * Inherits session cookie options from the cookie options above
     */
    'session.autostart' => true,
    'session.lifetime' => 0,
    'session.name' => '__based_sess_id',
    'session.save_path' => srcpath('var/sessions'),

    /**
     * Default theme locale
     */
    'theme.locale' => 'en_US',

    // Template path for custom pages
    'custom_page_template_path' => 'pages/',

    /**
     * Dashboard settings
     */

    // Items per page
    'dashboard.items_per_page' => 10,

    // Gallery items per page
    'dashboard.gallery_items_per_page' => 24,

    /**
     * Buddy Setting
     */

    'visitor_cookie_name' => 'visitor_search_limit',

    'visitor_limit' => 5,


    /**
     * Site Settings
     */

    // Whether registration is enabled or not
    'site.registration_enabled' => true,

    // Seconds since last refresh till user will be marked as in-active
    'site.user_online_lifespan' => 120,

    // whether to check for updates or not
    'site.check_for_updates' => false,

    // update checking interval
    // default: 3600 - 1 hour
    'site.update_check_interval' => 3600,

    /**
     * Internal settings
     */

    //  Min. username length
    'internal.username_minlength' => 5,
    // Max. username length
    'internal.username_maxlength' => 31,

    //  Min. password length
    'internal.password_minlength' => 6,

    // Max. file size for user avatar
    'internal.avatar_maxsize' => "5M",
    // Max. file size for user cover
    'internal.cover_maxsize' => "5M",

    /**
     * Authentication settings
     */

    // Force email verification on the user
    'auth.force_email_verification' => true,

    // Time (in seconds) the user will have to wait to request a new email activation token
    'auth.email_verify_wait_timespan' => 600,

    // Time (in seconds) the user will have to wait to request a new forgot password token
    'auth.forgotpass_wait_timespan' => 600,

    // Number of failed logins till the user IP will be blocked temporarily,  0 = disable this behavior
    'auth.max_failed_login_attempt' => 3,

    // Time (in seconds) the client will be blocked after client has reached the max. failed login limit
    'auth.login_block_timespan' => 900,

    // Time (in PHP strtotime() format) the user will be remembered/auto-logged
    'auth.cookie_token_lifespan' => '+6 Months',

    // Time (in PHP strtotime() format) the user's email verification token will remain valid
    'auth.mail_verify_token_lifespan' => '+5 Day',

    // Time (in PHP strtotime() format) the user's forgot password token will remain valid
    'auth.forgot_pass_token_lifespan' => '+2 Day',

    // Number of accounts allowed to be created within the same IP, 0 = disable this behavior
    'auth.max_account_per_ip' => 0,

    // API Token Lifespan
    'api_token_lifespan' => '+6 Months',


    /**
     * Middlewares
     */

    // Enables the form honeypot
    'enable_honeypot' => true,
    // Input key for honeypot
    'honeypot_key' => '__required_for_safety__',

    // Enables the CSRF guard
    'enable_csrfguard' => true,
    // CSRF Identifier
    'csrf_key' => 'csrf_token',

    /**
     * Caching
     */

    // Time of seconds to cache the menus
    'menu_cache_lifetime' => 86400,

    /**
     * Database Settings
     *
     * @var array
     */
    'db' => require(__DIR__ . '/db.php'),
];
