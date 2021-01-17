<?php

namespace spark\controllers;

/**
 * Parent Controller
 *
 * All controllers should extend this class
 *
 */
class Controller
{
    /**
     * Construct the Controller
     *
     * All Controllers must call this in their constructor exactly once!
     */
    public function __construct()
    {
        $app = app();

        $locale = get_cookie_locale() ? get_cookie_locale() : get_option('site_language', 'en_US');
        load_theme_locale($locale);

        $localeType = 'ltr';
        $htmlClass = $localeType;

        if ($app->locale->isRtl($locale)) {
            $localeType = 'rtl';
            $htmlClass = 'rtl';
        }

        view_set('locale_direction', $localeType);
        registry_store('locale_direction', $localeType, true);

        // Prevent caching at first
        // This header can be overwritten for dynamic cache-able content later
        $app->response->headers->set('Cache-Control', 'private,max-age=0');
        // Varies from time to time
        $app->response->headers->set('Vary', 'Accept-Encoding,User-Agent');
        $app->response->headers->set('X-Content-Type-Options', 'nosniff');

        if (is_ajax()) {
            slim_no_cache_headers();
        }

        $app->user->setupUser();

        $separator = '-';

        // Set basic template values
        $title = get_option('site_name') . " {$separator} " . get_option('site_tagline');
        $description = get_option('site_description');
        $name = get_option('site_name');
        $image = ensure_abs_url(get_option('opengraph_image', 'site/assets/img/og-image.png'));
        $url = get_current_route_uri();

        $fbAppID = get_option('facebook_app_id');
        $locale = get_theme_active_locale();

        $redirectURI = '?redirect_to=' . rawurlencode($url);

        $routeName = get_current_route_name();

        $redirectTo = $app->request->get('redirect_to');


        if (strpos($routeName, 'auth') === 0) {
            $redirectURI = '?redirect_to=' . rawurlencode(url_for('site.home'));
        }

        $nonVerifiedRoutes = ['auth.activation', 'auth.activation_post', 'auth.verify_action'];

        $currentRouteName = get_current_route_name();


        if (is_logged()) {
            if (!$app->user->isVerified() && !in_array($currentRouteName, $nonVerifiedRoutes) && $app->config('auth.force_email_verification')) {
                if (is_ajax()) {
                     json(['redirect' => url_for('auth.activation')]);
                     return $app->stop();
                }
                return redirect_to('auth.activation');
            }
        }

        $preferences = [
            'safesearch' => get_option('safesearch_status', 'off'),
            'new_window' => sp_int_bool_literal(get_option('search_links_newwindow', 1)),
            'search_autocomplete' => sp_int_bool_literal(get_option('search_autocomplete', 1)),
            'backgrounds' => sp_int_bool_literal(get_option('enable_backgrounds', 0)),
            'darkmode' => sp_int_bool_literal(get_option('enable_darkmode', 0)),
        ];

        switch (get_cookie('app.safesearch')) {
            case 'moderate':
                $preferences['safesearch'] = 'moderate';
                break;
            case 'strict':
                $preferences['safesearch'] = 'strict';
                break;
            default:
                $preferences['safesearch'] = 'off';
                break;
        }

        // Loose type checking is intentional in the following checks below:

        if (get_cookie('app.darkmode') == '0' || get_cookie('app.darkmode') == '1') {
            $preferences['darkmode'] = sp_int_bool_literal(get_cookie('app.darkmode'));
        }

        if (get_cookie('app.new_window') == '0' || get_cookie('app.new_window') == '1') {
            $preferences['new_window'] = sp_int_bool_literal(get_cookie('app.new_window'));
        }

        if (get_cookie('app.search_autocomplete') == '0' || get_cookie('app.search_autocomplete') == '1') {
            $preferences['search_autocomplete'] = sp_int_bool_literal(get_cookie('app.search_autocomplete'));
        }

        if (get_cookie('app.backgrounds') == '0' || get_cookie('app.backgrounds') == '1') {
            $preferences['backgrounds'] = sp_int_bool_literal(get_cookie('app.backgrounds'));
        }

        $app->config('preferences', $preferences);

        $authRoute = strpos(get_current_route_name(), 'auth.') === 0;

        if ($preferences['darkmode'] && !$authRoute) {
            registry_store('darkmode', true, true);
            $htmlClass .= ' darkmode';
        } else {
            registry_store('darkmode', false, true);
            $htmlClass .= ' lightmode';
        }

        view_set('preferences', $preferences);

        $windowGlobalApp = [
            'baseURI'              => base_uri(),
            'currentRouteURI'      => js_string(get_current_route_uri()),
            'currentRouteName'     => js_string(get_current_route_name()),
            'csrfKey'              => view_get('csrf_key'),
            'csrfToken'            => view_get('csrf_token'),
            'recaptchaSiteKey'     => e_attr(get_option('google_recaptcha_site_key', '')),
            'isLogged'             =>  is_logged() ? true : false,
            'enableAjaxNavigation' => (int) get_option('enable_ajax_nav', 1) ? true : false,
            'suggestionEndpoint'   => e_attr(url_for('site.suggest_queries')),
            'settings'             => $preferences,
        ];


        $searchOpen = trim($app->request->get('q'));

        if (!empty($searchOpen)) {
            $searchOpen = true;
        } else {
            $searchOpen = false;
        }

        $searchLogoUrl = ensure_abs_url(get_option('search_logo'));

        if ($preferences['darkmode']) {
            $searchLogoUrl = ensure_abs_url(get_option('search_logo_dark'));
        }

        $windowAlerts = [];

        view_data(
            [
                'title'                  => $title,
                'title_append_site_name' => true,
                'title_separator'        => $separator,
                'meta.description'       => $description,
                'meta.name'              => $name,
                'meta.image'             => $image,
                'meta.url'               => $url,
                'meta.type'              => 'website',
                'meta.locale'            => $locale,
                'meta.noindex'           => false,
                'meta.nocache'           => false,
                'meta.fb_app_id'         => $fbAppID,
                'redirect_to_query'      => $redirectURI,
                'hide_header'            => false,
                'hide_footer'            => false,
                'header_template'        => 'shared/header.php',
                'footer_template'        => 'shared/footer.php',
                'search_open'            => $searchOpen,
                'window_js_app'          => $windowGlobalApp,
                'search_logo_url'        => $searchLogoUrl,
                'html_class'             => $htmlClass,
                'window_alerts' => $windowAlerts,
            ]
        );


        // Load active theme functions
        require current_theme_path('skin.php');

        $jsLocaleKeys = [
            'num_1', 'num_2', 'num_3', 'num_4', 'num_5', 'num_6', 'num_7', 'num_8', 'num_9', 'num_0',
            'ajax-error', 'okay', 'choose-file', 'invalid-captcha', 'validation-max-file-size', 'no-search-results',
            'suggestions', 'make-sure-spelling', 'try-different-keywords', 'try-general-keywords', 'try-fewer-keywords'
        ];

        for ($i=1; $i < 13; $i++) {
            $jsLocaleKeys[] = "month-{$i}";
        }

        for ($i=0; $i < 7; $i++) {
            $jsLocaleKeys[] = "day-{$i}";
        }

        $windowJSLocale = [];

        foreach ($jsLocaleKeys as $key) {
            $windowJSLocale[$key] = js_string(__($key, _T));
        }

        view_set('window_js_locale', $windowJSLocale);

        // Load frontend tasks file
        if (is_frontend()) {
            require trailingslashit(__DIR__) . 'frontend_task.php';
        } else {
            require trailingslashit(__DIR__) . 'backend_task.php';
        }
    }
}
