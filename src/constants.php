<?php

/**
 * Toggle developer mode
 *
 * @var boolean
 */

define('DEV_MODE', false);

/**
 * Core constant to check if we're inside the app
 *
 * @var booleam
 */
define('SPARKIN', true);

/**
 * Toggle demo mode
 *
 * @var boolean
 */
define('DEMO_MODE', false);


/**
 * Script version
 *
 * @var integer|float
 */
define('APP_VERSION', "1.0");

/**
 * Current App Name
 *
 * @var string
 */
define('APP_NAME', 'Based');


/**
 * Path to the app directory
 *
 * @var string
 */
define('SRCPATH', str_replace('\\', '/', __DIR__) . '/');

/**
 * Path to the base directory
 *
 * @var string
 */
define('BASEPATH', dirname(SRCPATH) . '/');

/**
 * Name of the site directory
 *
 * @var string
 */
define('SITE_DIR', 'site');

/**
 * Name of the theme directory
 *
 * @var string
 */
define('THEME_DIR', SITE_DIR . '/themes');

/**
 * Name of the avatar directory
 */
define('AVATAR_DIR', SITE_DIR . '/avatars');

/**
 * Name of the frontend locale textdomain
 *
 * @var string
 */
define('_T', 'theme');


/**
 * Name of the uploads directory
 *
 * @var string
 */
define('UPLOADS_DIR', SITE_DIR . '/uploads');

/**
 * Base Controller namespace
 *
 * @var string
 */
define('CONTROLLER_NAMESPACE', '\\spark\\controllers\\');


// Path to cache directory (must be writeable)
define('THUMB_CACHE', SRCPATH . 'var/cache/thumbnails/');
define('THUMB_CACHE_AGE', 86400);         // Duration of cached files in seconds
define('THUMB_BROWSER_CACHE', true);          // Browser cache true or false
define('SHARPEN_MIN', 12);            // Minimum sharpen value
define('SHARPEN_MAX', 28);            // Maximum sharpen value
define('ADJUST_ORIENTATION', true);          // Auto adjust orientation for JPEG true or false
define('JPEG_QUALITY', 100);           // Quality of generated JPEGs (0 - 100; 100 being best)
