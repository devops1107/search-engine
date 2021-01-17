<?php

namespace spark\controllers\Dashboard;

use Valitron\Validator;
use spark\controllers\Controller;
use spark\drivers\Filter\Xss;
use spark\models\AttemptModel;
use spark\models\TokenModel;

/**
* Controller for Settings Page
*
* @package spark
*/
class DashboardSettingsController extends DashboardController
{
    protected $captchaLocations;

    protected $safeSearch = ['off', 'moderate', 'strict'];

    public function __construct()
    {
        parent::__construct();



        breadcrumb_add('dashboard.settings', __('Settings'), '#settings');

        // Load form validator
        sp_enqueue_script('parsley', 2, ['dashboard-core-js']);
        sp_enqueue_script('jquery-form-toggle', 2);

        $this->captchaLocations = [
            'auth.signin' => __('Sign In Page'),
            //'auth.register' => __('Register Page'),
            'auth.forgotpass' => __('Forgot Password Page'),
        ];

        $settings = [
            'general' => [
                'type' => 'link',
                'url' => url_for('dashboard.settings', ['type' => 'general']),
                'label' => __('General'),
                'active_var' => 'settings_general__active'
            ],
            'site' => [
                'type' => 'link',
                'url' => url_for('dashboard.settings', ['type' => 'site']),
                'label' => __('Site'),
                'active_var' => 'settings_site__active'
            ],
            'appearance' => [
                'type' => 'link',
                'url' => url_for('dashboard.settings', ['type' => 'appearance']),
                'label' => __('Looks'),
                'active_var' => 'settings_appearance__active'
            ],
            'search' => [
                'type' => 'link',
                'url' => url_for('dashboard.settings', ['type' => 'search']),
                'label' => __('Search'),
                'active_var' => 'settings_search__active'
            ],
            'ads' => [
                'type' => 'link',
                'url' => url_for('dashboard.settings', ['type' => 'ads']),
                'label' => __('Ads'),
                'active_var' => 'settings_ads__active'
            ],
            /*'social' => [
                'type' => 'link',
                'url' => url_for('dashboard.settings', ['type' => 'social']),
                'label' => __('Social'),
                'active_var' => 'settings_social__active'
            ],*/
            'services' => [
                'type' => 'link',
                'url' => url_for('dashboard.settings', ['type' => 'services']),
                'label' => __('Services'),
                'active_var' => 'settings_services__active'
            ],
            'email' => [
                'type' => 'link',
                'url' => url_for('dashboard.settings', ['type' => 'email']),
                'label' => __('E-Mail'),
                'active_var' => 'settings_email__active'
            ],
            'debug' => [
                'type' => 'link',
                'url' => url_for('dashboard.settings', ['type' => 'debug']),
                'label' => __('Debugging'),
                'active_var' => 'settings_debug__active'
            ],
        ];

        view_set('settings__active', 'active');
        view_set('subsettings', $settings);
    }

    public function index($type)
    {
        $app = app();
        $template = "admin::settings/{$type}.php";

        if (!has_template($template)) {
            return $app->redirect('/404');
        }

        $data = [
            "settings_{$type}__active" => 'active',
            'type' => $type,
        ];

        if ($type == 'services') {
            $data['checked_locations'] = get_option_json('captcha_locations');
            $data['captcha_locations'] = $this->captchaLocations;
        }

        if ($type == 'search') {
            $data['safesearch'] = $this->safeSearch;
        }

        return view($template, $data);
    }

    public function indexPOST($type)
    {
        $app = app();
        $type = strtolower($type);
        $method = "{$type}POST";

        $template = "admin::settings/{$type}.php";

        if (!has_template($template)) {
            return $app->redirect('/404');
        }

        if ($method !== 'index' && is_callable([$this, $method])) {
            if (is_demo()) {
                flash('settings-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
                return redirect_to_current_route();
            }


            $this->{$method}();


            return;
        }

        return $app->redirect('/404');
    }

    /**
     * Update general settings
     *
     * @return
     */
    public function generalPOST()
    {
        $app = app();
        $req = $app->request;

        $needsCleaning = [
            'site_name', 'site_tagline', 'site_description', 'timezone'
        ];

        $data = [
            'site_name'        => $req->post('site_name'),
            'site_tagline'     => $req->post('site_tagline'),
            'site_description' => $req->post('site_description'),
            'timezone'         => $req->post('timezone'),
            'timezone'         => $req->post('timezone'),
            'header_scripts'   => $req->post('header_scripts'),
            'footer_scripts'   => $req->post('footer_scripts'),
        ];

        $v = new Validator($data);
        $v->labels([
            'site_name' => __('Site Name'),
            'site_favicon' => __('Site Favicon'),
            'site_tagline' => __('Site Tagline'),
            'site_description' => __('Site Description'),
            'timezone' => __('Site Timezone'),
        ]);
        $v->rule('required', ['site_name', 'site_tagline', 'site_description', 'timezone'])
          ->rule('timezone', 'timezone');

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());
            flash('settings-danger', $errors);
        } else {
            foreach ($data as $key => $value) {
                if (in_array($key, $needsCleaning)) {
                    $value = sp_strip_tags($value);
                }
                set_option($key, $value);
            }

            flash('settings-success', __('Settings were updated successfully.'));
        }

        return redirect_to_current_route();
    }

    public function sitePOST()
    {
        $app = app();
        $req = $app->request;

        $xss = new Xss;

        $needsCleaning = ['opengraph_image', 'site_logo', 'search_logo_dark',
                        'site_favicon', 'dark_logo', 'search_logo'];
        $needsXssFilter = [];

        $data = [
            'site_logo'              => $req->post('site_logo'),
            'dark_logo'              => $req->post('dark_logo'),
            'search_logo_dark'       => $req->post('search_logo_dark'),
            'search_logo'            => $req->post('search_logo'),
            'site_favicon'           => $req->post('site_favicon'),
            'opengraph_image'        => $req->post('opengraph_image'),
            'sitemap_links_per_page' => (int) $req->post('sitemap_links_per_page'),
            'rss_items_per_page'     => (int) $req->post('rss_items_per_page'),
        ];

        foreach ($data as $key => $value) {
            if (in_array($key, $needsCleaning)) {
                $value = sp_strip_tags($value);
            }

            if (in_array($key, $needsXssFilter)) {
                $value = $xss->filter($value);
            }

            set_option($key, $value);
        }


        flash('settings-success', __('Settings were updated successfully.'));
        return redirect_to_current_route();
    }

    public function appearancePOST()
    {
        $app = app();
        $ignore = ['theme_home_max_engines_count'];

        $data = [
            'site_language'                => $app->request->post('site_language'),
            'theme_home_max_engines_count' => (int) $app->request->post('theme_home_max_engines_count'),
            'home_logo_width'              => (int) $app->request->post('home_logo_width'),
            'search_logo_width'            => (int) $app->request->post('search_logo_width'),
            'enable_backgrounds'           => sp_int_bool($app->request->post('enable_backgrounds')),
            'enable_darkmode'              => sp_int_bool($app->request->post('enable_darkmode')),
            'show_engines_in_offcanvas'    => sp_int_bool($app->request->post('show_engines_in_offcanvas')),
            'enable_ajax_nav'              => sp_int_bool($app->request->post('enable_ajax_nav')),
            'serp_link_color'              => trim($app->request->post('serp_link_color')),
            'serp_domain_color'            => trim($app->request->post('serp_domain_color')),
            'serp_text_color'              => trim($app->request->post('serp_text_color')),
            'home_logo_align'              => trim($app->request->post('home_logo_align')),
        ];

        if (!in_array($data['home_logo_align'], ['left', 'center', 'right'])) {
            unset($data['home_logo_align']);
        }

        // Make sure the language exists
        $locales = get_theme_locales();
        if (!isset($locales[$data['site_language']])) {
            unset($data['site_language']);
        }

        foreach ($data as $key => $value) {
            // Strip values
            if (!in_array($key, $ignore)) {
                $value = sp_strip_tags($value);
            }

            set_option($key, $value);
        }


        flash('settings-success', __('Settings were updated successfully.'));
        return redirect_to_current_route();
    }

    public function servicesPOST()
    {
        $app = app();
        $req = $app->request;

        $needsCleaning = ['google_recaptcha_secret_key', 'google_recaptcha_site_key', 'facebook_app_id',
        'onesignal_app_id', 'onesignal_api_key'];

        $data = [
            'google_recaptcha_secret_key' => $req->post('google_recaptcha_secret_key'),
            'google_recaptcha_site_key'   => $req->post('google_recaptcha_site_key'),
            'facebook_app_id'             => $req->post('facebook_app_id'),
            'captcha_enabled'             => sp_int_bool($req->post('captcha_enabled')),
            'onesignal_enabled'           => sp_int_bool($req->post('onesignal_enabled')),
            'onesignal_app_id'            => $req->post('onesignal_app_id'),
            'onesignal_api_key'           => $req->post('onesignal_api_key'),
        ];

        foreach ($data as $key => $value) {
            if (in_array($key, $needsCleaning)) {
                $value = sp_strip_tags($value);
            }

            set_option($key, $value);
        }


        $ignoreCaptchas = (array) $req->post('ignore_captcha_locations', []);

        $captchaValue = [];

        foreach ($ignoreCaptchas as $key => $value) {
            if (isset($this->captchaLocations[$key])) {
                $captchaValue[$key] = sp_int_bool($value);
            }
        }

        set_option('captcha_locations', json_encode($captchaValue));


        flash('settings-success', __('Settings were updated successfully.'));
        return redirect_to_current_route();
    }

    public function socialPOST()
    {
        $app = app();
        $req = $app->request;

        $fields = [
            'facebook_username', 'twitter_username', 'youtube_username',
            'instagram_username', 'linkedin_username', 'vk_username'
        ];

        foreach ($fields as $key) {
            $value = sp_strip_tags(trim($req->post($key)), true);
            set_option($key, $value);
        }

        flash('settings-success', __('Settings were updated successfully.'));

        return redirect_to_current_route();
    }

    public function searchPOST()
    {
        $app = app();
        $req = $app->request;

        $fields = [
            'search_items_count' => (int) $app->request->post('search_items_count'),
            'image_search_items_count' => (int) $app->request->post('image_search_items_count'),
            'search_links_newwindow' => sp_int_bool($app->request->post('search_links_newwindow')),
            'show_entities' => sp_int_bool($app->request->post('show_entities')),
            'show_answers' => sp_int_bool($app->request->post('show_answers')),
            'search_log' => sp_int_bool($app->request->post('search_log')),
            'search_autocomplete' => sp_int_bool($app->request->post('search_autocomplete')),
            'safesearch_status' => trim($app->request->post('safesearch_status')),
        ];

        if (!in_array($fields['safesearch_status'], $this->safeSearch, true)) {
            unset($fields['safesearch_status']);
        }

        $needsCleaning = [];

        foreach ($fields as $key => $value) {
            if (in_array($key, $needsCleaning)) {
                $value = sp_strip_tags($value);
            }
            set_option($key, $value);
        }

        flash('settings-success', __('Settings were updated successfully.'));

        return redirect_to_current_route();
    }

    public function emailPOST()
    {
        $app = app();
        $req = $app->request;

        $data = [
            'site_email'        => $req->post('site_email'),
            'smtp_enabled'      => (bool) $req->post('smtp_enabled'),
            'smtp_auth_enabled' => (bool) $req->post('smtp_auth_enabled'),
            'smtp_host'         => sp_strip_tags($req->post('smtp_host')),
            'smtp_port'         => (int) $req->post('smtp_port'),
            'smtp_username'     => sp_strip_tags($req->post('smtp_username')),
            'smtp_password'     => $req->post('smtp_password'),
            'smtp_secure'     => trim($req->post('smtp_secure')),
        ];

        if (!filter_var($data['site_email'], FILTER_VALIDATE_EMAIL)) {
            unset($data['site_email']);
        }

        $protocols = ['', 'ssl', 'tls'];

        if (!in_array($data['smtp_secure'], $protocols, true)) {
            unset($data['smtp_secure']);
        }

        foreach ($data as $key => $value) {
            set_option($key, $value);
        }

        flash('settings-success', __('Settings were updated successfully.'));

        return redirect_to_current_route();
    }

    public function adsPOST()
    {
        $app = app();
        $req = $app->request;

        $data = [];

        // No need to proccess anything here
        for ($i=1; $i < 4; $i++) {
            $data["ad_unit_{$i}"] = $req->post("ad_unit_{$i}");
        }

        foreach ($data as $key => $value) {
            set_option($key, $value);
        }

        flash('settings-success', __('Settings were updated successfully.'));

        return redirect_to_current_route();
    }

    public function apiPOST()
    {
        $app = app();
        $req = $app->request;

        $actions = [
            'regen_server_key' => sp_int_bool($req->post('regen_server_key')),
        ];

        if ($actions['regen_server_key']) {
            $key = str_random_secure(40);

            set_option('server_api_key', $key);
        }

        flash('settings-success', __('Actions were performed successfully'));

        return redirect_to_current_route();
    }

    public function debugPOST()
    {
        // We may need a lot of time to run
        ignore_user_abort(1);
        set_time_limit(0);

        $app = app();
        $req = $app->request;

        $actions = [
            'clear_tokens' => sp_int_bool($req->post('clear_tokens')),
            'clear_logs' => sp_int_bool($req->post('clear_logs')),
            'clear_attempts' => sp_int_bool($req->post('clear_attempts')),
            'flush_cache' => sp_int_bool($req->post('flush_cache')),
            'regen_cron_token' => sp_int_bool($req->post('regen_cron_token')),
        ];

        if ($actions['regen_cron_token']) {
            $token = str_random_secure(10);
            set_option('spark_cron_job_token', $token);
        }

        if ($actions['flush_cache']) {
            // Purge site wide cache
            $app->cache->clear();

            // clear thumbnails cache
            $path = trailingslashit(THUMB_CACHE) . '*.img.txt';
            $thumbnails = glob($path);

            foreach ($thumbnails as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }

        if ($actions['clear_logs']) {
            // clear thumbnails cache
            $path = srcpath('var/logs/*.log');
            $logs = glob($path);

            foreach ($logs as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }

        if ($actions['clear_attempts']) {
            $attemptModel = new AttemptModel;
            $attemptModel->truncate();
        }

        if ($actions['clear_tokens']) {
            $tokenModel = new TokenModel;
            $tokenModel->clearExpiredTokens();
        }


        flash('settings-success', __('Actions were performed successfully'));

        return redirect_to_current_route();
    }

    public function themeOptions($theme)
    {
        $themeManager = app()->theme;

        $template = registry_read("theme.{$theme}__options_template");

        // Make sure the plugin has registered templates
        if (!$template) {
            flash('dashboard-danger', sprintf("Theme <em>%s</em> doesn't have any options registered", sp_strip_tags($theme)));
            return redirect_to('dashboard');
        }


        breadcrumb_add("dashboard.themes", __('Themes'), url_for('dashboard.themes'));

        // grab theme meta
        $themeMeta = $themeManager->getThemeMeta($theme);

        // Breadcrumbs
        breadcrumb_add("dashboard.settings.{$theme}", sprintf(__('%s Options'), $themeMeta['name']));

        // Mark current option page as active
        view_set("theme-{$theme}-options__active", 'active');

        $data = [
            'theme_options__active' => 'active',
            'item' => $theme,
            'meta' => $themeMeta,
            'type' => 'theme',
        ];

        return view($template, $data);
    }

    public function themeOptionsPOST($theme)
    {
        $themeManager = app()->theme;

        $template = registry_read("theme.{$theme}__options_template");
        $callback = registry_read("theme.{$theme}OnOptionsSubmit");

        // Make sure the plugin has registered templates
        if (!$template) {
            flash('dashboard-danger', sprintf("Theme <em>%s</em> doesn't have any options registered", sp_strip_tags($theme)));
            return redirect_to('dashboard');
        }

        $callback();

        return redirect_to_current_route();
    }
}
