<?php
/*
Theme Name: Oishy
Theme URI: http://github.com/MirazMac
Description: Oishy is the default theme for Based. This theme was inspired by various search engines, including but not limited to Google, Bing, Duckduckgo. This theme should be cloned to create new themes.
Author: Miraz Mac
Version: 1.0
Author URI: https://mirazmac.com
*/

defined('SPARKIN') or die('xD');

$app = app();

register_nav_menu('offcanvas-nav', __('offcanvas-nav-menu', _T));
register_nav_menu('footer-nav', __('footer-nav-menu', _T));

// Theme Stylesheet
sp_register_style(
    'theme-styles',
    current_theme_uri('assets/css/styles.css'),
    ['abspath' => current_theme_path('assets/css/styles.css')]
);

// Google Webfont
sp_register_style(
    'theme-webfont',
    'https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500&display=swap'
);

// Theme Bootstrap Bundle
sp_register_script(
    'vendor-js',
    current_theme_uri('assets/js/vendor.js'),
    ['abspath' => current_theme_path('assets/js/vendor.js')]
);

// Theme JS
sp_register_script(
    'theme-js',
    current_theme_uri('assets/js/app.js'),
    ['abspath' => current_theme_path('assets/js/app.js')]
);

sp_register_script(
    'custom-js',
    current_theme_uri('assets/js/custom.js'),
    ['abspath' => current_theme_path('assets/js/custom.js')]
);

// Recaptcha
sp_register_script(
    'google-recaptcha-custom',
    'https://www.google.com/recaptcha/api.js?onload=refreshRecaptcha&render=explicit'
);


// Frontend tasks
if (is_frontend()) {
    // Enqueue assets
    sp_enqueue_style('theme-styles');
    sp_enqueue_style('theme-webfont');

    if ((int) get_option('captcha_enabled')) {
        sp_enqueue_script('google-recaptcha-custom', 1);
    }

    sp_enqueue_script('vendor-js');
    // Load theme js in footer so all the elements are available to it
    sp_enqueue_script('theme-js', 2);
}
