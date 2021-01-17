<?php

namespace spark\controllers\Site;

use Valitron\Validator;
use spark\controllers\Controller;
use spark\drivers\Auth\Auth;
use spark\drivers\Http\DuckDuckGoInstantAnswer;
use spark\drivers\Http\Http;
use spark\drivers\I18n\Locale;
use spark\drivers\Mail\Mailer;
use spark\drivers\Views\InstantAnswer;
use spark\models\ContentModel;
use spark\models\EngineModel;
use spark\models\MetaModel;
use spark\models\QueryModel;
use spark\models\UserModel;

/**
* SiteController
*
* @package spark
*/
class SiteController extends Controller
{
    protected $safesearchTypes = ['off', 'moderate', 'strict'];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Homepage
     *
     * @return
     */
    public function home()
    {
        $engineModel = new EngineModel;

        $homePageEnginesCount = (int) get_option('home_engines_count', 10);
        $defaultEngine = (int) get_option('default_engine', 1);

        $engines = $engineModel->readMany(
            [
                'engine_name', 'engine_id'
            ],
            0,
            $homePageEnginesCount,
            [
                'sort' => 'order-first'
            ]
        );

        $navEngines = $engines;

        $dropDownEngines = [];

        $maxEngines = (int) get_option('theme_home_max_engines_count');

        if (count($engines) > $maxEngines) {
            $offset = count($engines) - $maxEngines;
            $dropDownEngines = array_slice($engines, -$offset);

            $navEngines = array_slice($navEngines, 0, $maxEngines);
        }

        $engine = $engineModel->read($defaultEngine, ['engine_name']);

        $backgroundsEnabled = config('preferences')['backgrounds'];
        $background = null;

        if ($backgroundsEnabled) {
            $files = glob(sitepath('backgrounds/*.{jpg,JPG,jpeg,JPEG,png,PNG,gif,GIF,webp,WEBP}'), GLOB_BRACE);
            $selected = $files[array_rand($files)];

            $background = site_uri("backgrounds/" . basename($selected));
        }

        $bodyClass = 'home';

        $logoURL = sp_logo_uri();

        if ($backgroundsEnabled) {
            $bodyClass .= ' has-background';
            $logoURL = ensure_abs_url(get_option('dark_logo'));
        }

        if (config('preferences')['darkmode']) {
            $logoURL = ensure_abs_url(get_option('dark_logo'));
        }

        $logoAlignment = get_option('home_logo_align', 'center');
        $logoAlignmentClass = "text-{$logoAlignment}";


        $data = [
            'body_class'             => $bodyClass,
            'logo_url'               => $logoURL,
            'max_engines_count'      => $maxEngines,
            'hide_header'            => true,
            'hide_footer'            => false,
            'default_engine'         => $defaultEngine,
            'is_home'                => true,
            'backgrounds_enabled'    => $backgroundsEnabled,
            'title_append_site_name' => false,
            'engines'                => $engines,
            'nav_engines'            => $navEngines,
            'engine'                 => $engine,
            'dropdown_engines'       => $dropDownEngines,
            'background'             => $background,
            'show_engines_offcanvas' => (int) get_option('show_engines_in_offcanvas', 1),
            'logo_alignment'         => $logoAlignment,
            'logo_alignment_class'   => $logoAlignmentClass,
            'limit-message'          => ''
        ];

        if(!is_logged()){

            $cookie_name = config('visitor_cookie_name');

            if(!isset($_COOKIE[$cookie_name])) {

                $cookie_value = getCookieName();
                setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
                session_set($cookie_value, 0);

            }
            else{

                $limit = (int) session_get($_COOKIE[$cookie_name]);

                if($limit > config('visitor_limit')){

                    $data['limit-message'] = 'Please sign up! Sorry, Your search is unavailable';
                    return ajax_view('home/home.php', $data);

                }
            }
        }
        else{

            print_r("verify user");
        }

        return ajax_view('home/home.php', $data);
    }
    /**
     * Search page
     */
    public function search()
    {
        // 12/14/2020 P19901107

        $auth_id = '';

        if(!is_logged()){

            $cookie_name = config('visitor_cookie_name');

            if(!isset($_COOKIE[$cookie_name])) {

                $redirectURI = get_redirect_to_uri(url_for('site.home'));

                if (is_ajax()) {
                    return ajax_form_json(['redirect' => $redirectURI]);
                }
                return redirect($redirectURI);

            } else {

                $limit = (int) session_get($_COOKIE[$cookie_name]);

                if($limit > config('visitor_limit') ){

                    $redirectURI = get_redirect_to_uri(url_for('site.home'));

                    if (is_ajax()) {

                        return ajax_form_json([

                            'message'=>'Please register! Available search is limited.',
                            'redirect' => $redirectURI,
                            'dismissable'=>true,
                            'type'=>'danger'
                        ]);
                    }
                    return redirect_to('site.home');
                }
                else{
                    session_set($_COOKIE[$cookie_name],$limit+1);
                    print_r($limit);
                }
            }
        }
        else{

            $auth_id = current_user_ID();
        }


        //
        $engineModel = new EngineModel;
        $app = app();
        $engineID = (int) $app->request->get('engine');
        $query = trim($app->request->get('q'));

        if (empty($query)) {
            if (is_ajax()) {
                return json(['redirect' => url_for('site.home')]);
            }

            return redirect_to('site.home');
        }

        $defaultEngine = (int) get_option('default_engine', 1);

        if (!$engineID) {
            $engineID = $defaultEngine;
        }

        $engine = $engineModel->read($engineID);

        if (!$engine) {
            return $app->notFound();
        }

        $engines = $engineModel->readMany(
            [
                'engine_name', 'engine_id'
            ],
            0,
            100,
            [
                'sort' => 'order-first'
            ]
        );

//        $showAds = (int) $engine['engine_show_ads'] ? 'true' : 'false';
        $showAds = 'true';

        $showThumbnail = (int) $engine['engine_show_thumb'] ? 'true' : 'false';

        $cseID = js_string($engine['engine_cse_id']);

        $locale = get_theme_active_locale();

        $script = <<<SCRIPT
        var engine = {
                showThumbnails: {$showThumbnail},
                showAds: {$showAds},
        };

        (function() {

            var cx = '{$cseID}';
            var gcse = document.createElement('script');
            gcse.type = 'text/javascript';
            gcse.id = 'cse-{$engineID}';
            gcse.async = false;
            // Disable rocketloader
            gcse.dataset.cfasync = false;
            gcse.src = 'https://cse.google.com/cse.js?hl={$locale}&cx=' + cx;
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(gcse, s);
        })();
SCRIPT;


        $isDefaultEngine = (int) get_option('default_engine', 1) === (int) $engineID;

        $isImage = (int) $engine['engine_is_image'];

        $ddgAnswer = [];
        $instantAnswer = false;

        if (!$isImage) {
            if ((int) get_option('show_entities')) {
                $ddgAnswer = (new DuckDuckGoInstantAnswer)->getAnswer($query);
            }

            if ((int) get_option('show_answers')) {
                $instantAnswer = (new InstantAnswer)->boot($query);
            }
        }

        $logSearches = (int) get_option('search_log');

        if ($logSearches) {
            $queryModel = new QueryModel;
            $db = $queryModel->db();
            $time = time();

            $stmt = $db->prepare("INSERT INTO {$queryModel->getTable()} ( query_term, created_at, updated_at ) VALUES ( :term, :ctime, :utime ) ON DUPLICATE KEY UPDATE query_count = query_count + 1, updated_at = {$time};");

            $stmt->bindValue(':term', limit_string(sp_strip_tags($query), 250), \PDO::PARAM_STR);
            $stmt->bindValue(':ctime', $time, \PDO::PARAM_INT);
            $stmt->bindValue(':utime', $time, \PDO::PARAM_INT);

            $stmt->execute();
        }


        $preferences = config('preferences');

        $safeSearch = $preferences['safesearch'];
        $newWindow = $preferences['new_window'];

        $target = '_self';

        if ($newWindow) {
            $target = '_blank';
        }


        // Attributes
        $attrs = [
            'enableHistory' => false,
            'safeSearch' => $safeSearch,
            'linkTarget' => $target,
            'noResultsString' => sprintf(__('no-search-results', _T), $query),
        ];

        $attrText = '';

        // Attribues specific to image search
        if ($isImage) {
            $attrs['disableWebSearch'] = true;
            $attrs['enableImageSearch'] = true;
            $attrs['defaultToImageSearch'] = true;
            // Enabled only for mobile devices
            $attrs['mobileLayout'] = 'enabled';
            $attrs['imageSearchResultSetSize'] = (int) get_option('image_search_items_count', 20);
        } else {
            $attrs['webSearchResultSetSize'] = (int) get_option('search_items_count', 10);
            $attrs['mobileLayout'] = 'enabled';
        }

        foreach ($attrs as $key => $value) {
            $attrText .= " data-{$key}=\"{$value}\"";
        }

        $script .= "\n" . 'var cseAttrs = ' .  json_encode($attrs) . ';';

        $cseElement = '<div class="gcse-searchresults-only" ' . $attrText . '></div>';

        $bodyClass = 'search-results';

        if (!(int) $engine['engine_show_thumb']) {
            $bodyClass .= ' no-thumbnail';
        }


        if (!(int) $engine['engine_show_ads']) {
            $bodyClass .= ' no-ads';
        }

        $data = [
            'title'                => __('search-results-for', _T, ['q' => $query]),
            'body_class'           => $bodyClass,
            'current_engine_id'    => $engineID,
            'cse_script'           => $script,
            'cse_element'          => $cseElement,
            'engines'              => $engines,
            'ajax_form_change_url' => true,
            'search_query'         => $query,
            'is_image'             => $isImage,
            'answer'               => $ddgAnswer,
            'user_id'              => $auth_id
        ];


        if ($instantAnswer) {
            $data['ia_view'] = $instantAnswer['view'];
            $data['ia_data'] = $instantAnswer['data'];

            if (has_items($instantAnswer['extra'])) {
                $data = array_merge($data, $instantAnswer['extra']);
            }
        }

        $data["{$engine['engine_id']}_active"] = 'active';

        return ajax_view('search/search.php', $data);
    }


    /**
     * Suggests queries as the user types from Google auto-complete
     *
     * @return
     */
    public function suggestQueries()
    {
        $app = app();
        $q = $app->request->get('q');

        // Handle aborted connections
        // hopefully
        if (connection_aborted()) {
            return json([$q]);
        }

        if (empty($q)) {
            return json([$q]);
        }

        $locale = get_theme_active_locale();

        $url = "https://suggestqueries.google.com/complete/search?client=firefox&q={$q}&hl=$locale";
        $http = Http::getSession();
        try {
            $response = $http->get($url);
        } catch (\Exception $e) {
            // ssh!
            return json([$q]);
        }

        if (!$response->success) {
            return json([$q]);
        }

        $json = json_decode($response->body, true);

        if (empty($json[1])) {
            return json([$q]);
        }

        $data = [];
        //unset($json[1][0]);

        foreach ($json[1] as $value) {
            $data[] = $value;
        }

        return json($data);
    }

    /**
     * 404 Not Found
     *
     * @return
     */
    public function notFound()
    {
        return ajax_view('error/404.php');
    }

    /**
     * Access a page
     *
     * @param mixed  $slugOrID
     * @return
     */
    public function page($slugOrID)
    {
        $app = app();
        $contentModel = new ContentModel;

        $filters = [];
        $filters['where'][] = ['content_slug', '=', $slugOrID, 'OR'];
        $filters['where'][] = ['content_type', '!=', ContentModel::TYPE_PAGE];

        $page = $contentModel->db()->select()
        ->from($contentModel->getTable())
        ->where('content_slug', '=', $slugOrID)
        ->orWhere('content_id', '=', $slugOrID)
        ->where('content_type', '=', 'page')
        ->limit(1, 0)
        ->execute()
        ->fetch();

        if (!$page) {
            return $app->notFound();
        }

        $customTemplate = has_custom_template($page['content_slug']);

        $template = 'pages/page.php';

        if ($customTemplate) {
            $template = $customTemplate;
        }


        $method = strtolower($app->request->getMethod());

        // Add the breadcrumb
        breadcrumb_add('page', $page['content_title']);

        // Prepare description
        $description = limit_string(sp_strip_tags($page['content_body'], true), 300, '');

        $pageMeta = json_decode($page['content_meta'], true);

        if (!$pageMeta) {
            $pageMeta = [];
        }


        $data = [
            'title'            => $page['content_title'],
            'header_heading' => $page['content_title'],
            'meta.description' => $description,
            'page'             => $page,
            'page_meta'        => $pageMeta,
            'body_class'       => "page {$page['content_slug']}",
        ];


        if (!empty($pageMeta['image'])) {
            $data['meta.image'] = ensure_abs_url($pageMeta['image']);
        }

        if (!empty($pageMeta['description'])) {
            $data['meta.description'] = $pageMeta['description'];
        }

        return ajax_view($template, $data);
    }

    /**
     * Preferences Page
     */
    public function preferences()
    {
        $app = app();

        $safesearchTypes = $this->safesearchTypes;

        $data = [
            'title' => __('preferences', _T),
            'body_class' => 'preferences',
            'header_heading' => __('preferences', _T),
            'safesearch_types' => $safesearchTypes,
        ];

        return ajax_view('search/preferences.php', $data);
    }

    /**
     * Handles saving of the preferences
     *
     */
    public function preferencesPOST()
    {
        $app = app();

        $data = [
            'new_window'          => sp_int_bool($app->request->post('new_window')),
            'search_autocomplete' => sp_int_bool($app->request->post('search_autocomplete')),
            'backgrounds'         => sp_int_bool($app->request->post('backgrounds')),
            'darkmode'            => sp_int_bool($app->request->post('darkmode')),
            'safesearch'          => trim($app->request->post('safesearch')),
        ];

        if (!in_array($data['safesearch'], $this->safesearchTypes)) {
            unset($data['safesearch']);
        }

        foreach ($data as $key => $value) {
            set_cookie("app.{$key}", $value, '+6 Months');
        }

        $locale = trim($app->request->post('language'));

        if (!empty($locale)) {
            set_cookie(Locale::COOKIE_NAME, $locale, '+1 Year');
        }

        $msg = __('preferences-saved-successfully', _T);

        flash('preferences-success', $msg);

        $redirectURI = url_for('site.preferences');

        if (is_ajax()) {
            return json(['redirect' => $redirectURI]);
        }

        return redirect($redirectURI);
    }

    /**
     * Handles contact form
     *
     * @return
     */
    public function handleContactForm()
    {
        $app = app();
        $req = $app->request;

        $response = [];

        $data = [
            'name'    => $req->post('name'),
            'email'   => $req->post('email'),
            'subject' => sp_strip_tags($req->post('subject'), true),
            'message' => sp_strip_tags($req->post('message')),
        ];

        $v = new Validator($data);

        $v->labels([
            'email'   => __('email', _T),
            'subject'   => __('subject', _T),
            'name'    => __('name', _T),
            'message' => __('message', _T),
        ])->rule('required', ['email', 'name', 'message'])
          ->rule('email', 'email')
          ->rule('lengthBetween', 'subject', 10, 200)
          ->rule('lengthBetween', 'message', 20, 5000);

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());

            if (is_ajax()) {
                $response['message'] = $errors;
                return ajax_form_json($response);
            }

            flash('pages-danger', $errors);
            sp_store_post($data);
            return follow_referer_uri();
        }

        // Verify the captcha
        if (!sp_verify_recaptcha()) {
            if (is_ajax()) {
                $response['message'] = __('invalid-captcha', _T);
                return ajax_form_json($response);
            }

            flash('pages-danger', __('invalid-captcha', _T));
            sp_store_post($data);
            return follow_referer_uri();
        }

        $data['user_ip']    = $req->getIp();
        $data['user_agent'] = $req->getUserAgent();

        if (empty($data['subject'])) {
            $data['subject'] = __('Contact form E-Mail from ') . get_option('site_name');
        }

        $body = $app->view->fetch('admin::emails/contact.php', $data);

        $mailer = (new Mailer)->getPhpMailer(
            $data['email'],
            get_option('site_email'),
            $data['subject'],
            $body
        );

        try {
            $mailer->send();
        } catch (\Exception $e) {
            $msg = __('mailer-error', _T) . $e->getMessage();
            if (is_ajax()) {
                $response['message'] = $msg;
                return ajax_form_json($response);
            }
            sp_store_post($data);
            flash('pages-danger', $msg);
            return follow_referer_uri();
        }

        if (is_ajax()) {
            $response['message'] = __('contact-form-success', _T);
            $response['type'] = 'success';
            return ajax_form_json($response);
        }

        flash(
            'pages-success',
            __('contact-form-success', _T)
        );

        return follow_referer_uri();
    }

    /**
     * Changes the site locale
     *
     * @param  string $locale
     * @return
     */
    public function changeLocale($locale)
    {
        set_cookie(Locale::COOKIE_NAME, $locale, '+2 Year');
        return follow_referer_uri(base_uri());
    }

    /**
     * Runs cron based tasks
     *
     * @return
     */
    public function runTasks()
    {
        $app = app();
        $req = $app->request;
        $token = $req->get('token');

        // Must match the token
        if ($token !== get_option('spark_cron_job_token')) {
            return sp_not_permitted();
        }

        // Run the tasks

        logger()->info('Cron ran at: ' . date('d-m-Y h:i:s A'));
    }
}
