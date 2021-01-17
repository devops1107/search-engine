<?php

use spark\drivers\I18n\Locale;
use spark\drivers\Nav\MenuGenerator;
use spark\drivers\Nav\MenuStorage;
use spark\drivers\Nav\NavbarGenerator;
use spark\drivers\Nav\SidebarGenerator;

/**
 * Declares a template section
 *
 * @param  string $name
 * @param  string $emptyFallback
 * @return
 */
function section($name, $emptyFallback = '')
{
    echo app()->view()->getInstance()->section($name, $emptyFallback);
}

/**
 * Extends a template
 *
 * @param  string $template
 * @param  array  $data
 * @return
 */
function extend($template, array $data = [])
{
    echo app()->view()->getInstance()->extend($template, $data);
}

/**
 * Starts a block
 *
 * @param  string $name
 * @return
 */
function block($name)
{
    app()->view()->getInstance()->start($name);
}

/**
 * Ends a block
 *
 * @param  intger $appendMode
 * @return
 */
function endblock($appendMode = 1)
{
    app()->view()->getInstance()->end($appendMode);
}

/**
 * Inserts a template
 *
 * @param  string $template
 * @param  array  $data
 * @return
 */
function insert($template, array $data = [])
{
    $view = app()->view->getInstance();
    echo $view->render($template, $data);
}

/**
 * Checks if a template exists
 *
 * @param  string  $template
 * @return boolean
 */
function has_template($template)
{
    $view = app()->view->getInstance();
    return $view->exists($template);
}

/**
 * Register a new template namespace
 *
 * @param  string $namespace
 * @param  string $directory
 * @return boolean
 */
function register_templates($namespace, $directory)
{
    $view = app()->view->getInstance();
    return $view->addFolder($namespace, $directory);
}

/**
 * Access active theme meta
 *
 * @param  string  $key
 * @param  mixed   $fallback
 * @return mixed
 */
function sp_theme_meta($key, $fallback = false)
{
    return app()->theme->meta($key, $fallback);
}

/**
 * Renders svg icon
 *
 * @param  string $id
 * @param  string $class
 * @param  array  $attrs
 * @return string
 */
function svg_icon($id, $class = '', array $attrs = [])
{
    $id = e_attr($id);
    $class = 'svg-icon ' . e_attr($class);
    $attrTxt = '';

    foreach ($attrs as $key => $value) {
        if ($key === 'class') {
            continue;
        }

        $attrTxt .= ' ' . e_attr($key) . '="' . e_attr($value) . '"';
    }
    return '<svg class="' . $class . '"'. $attrTxt .'><use xlink:href="#' . $id . '"/></svg>';
}

/**
 * Renders header assets and tags
 *
 * @return
 */
function sp_head()
{
    $app = app();

    $t = $app->view->getInstance();

    $title = $t['title'];

    if ($t['title_append_site_name']) {
        $title .= " {$t['title_separator']} {$t['meta.name']}";
    }


    $meta = '<title>' . e($title) . '</title>' . "\n";

    if ($t['meta.nocache']) {
        $meta .= "\t" . '<meta http-equiv="cache-control" content="no-cache, no-store, must-revalidate">' . "\n";
        $meta .= "\t" . '<meta http-equiv="pragma" content="no-cache">' . "\n";
        $meta .= "\t" . '<meta http-equiv="expires" content="0">' . "\n";
    }

    // Sorry robots
    if ($t['meta.noindex']) {
        $meta .= "\t" . '<meta name="robots" content="noindex, nofollow">' . "\n";
    }

    $meta .= "\t" . '<meta name="description" content="' . e_attr($t['meta.description']) . '">' . "\n";
    $meta .= "\t" . '<meta name="image" content="' . e_attr($t['meta.image']) . '">' . "\n";
    $meta .= "\t" . '<!-- Schema.org Tags -->' . "\n";
    $meta .= "\t" . '<meta itemprop="name" content="' . e_attr($t['meta.name']) . '">' . "\n";
    $meta .= "\t" . '<meta itemprop="description" content="' . e_attr($t['meta.description']) . '">' . "\n";
    $meta .= "\t" . '<meta itemprop="image" content="' . e_attr($t['meta.image']) . '">' . "\n";

    $meta .= "\t" . '<!-- Opengraph Tags -->' . "\n";
    $meta .= "\t" . '<meta property="og:title" content="' . e_attr($t['title']) . '">' . "\n";
    $meta .= "\t" . '<meta property="og:description" content="' . e_attr($t['meta.description']) . '">' . "\n";
    $meta .= "\t" . '<meta property="og:image" content="' . e_attr($t['meta.image']) . '">' . "\n";
    $meta .= "\t" . '<meta property="og:url" content="' . e_attr($t['meta.url']) . '">' . "\n";
    $meta .= "\t" . '<meta property="og:site_name" content="' . e_attr($t['meta.name']) . '">' . "\n";
    $meta .= "\t" . '<meta property="og:locale" content="' . e_attr($t['meta.locale']) . '">' . "\n";
    $meta .= "\t" . '<meta property="og:type" content="' . e_attr($t['meta.type']) . '">' . "\n";
    // Facebook app ID
    if (!empty($t['meta.fb_app_id'])) {
        $meta .= "\t" . '<meta property="fb:app_id" content="' . e_attr($t['meta.fb_app_id']) . '">' . "\n";
    }

    echo($meta);

    $faviconURI = ensure_abs_url(get_option('site_favicon'));
    $favicon = '<link rel="icon" href="' . e_attr($faviconURI) . '">' . "\n";
    $favicon .= '<link rel="shortcut icon" href="' . e_attr($faviconURI) . '">' . "\n";
    echo("\t" . $favicon);
    echo("\t" . $app->asset->renderHtml());

    // Header scripts/codes
    if (is_frontend()) {
        echo get_option('header_scripts');
    }
}

/**
 * Renders footer assets and tags
 *
 * @return
 */
function sp_footer()
{
    echo app()->asset->renderHtml(2);

    // footer scripts/codes
    if (is_frontend()) {
        echo get_option('footer_scripts');
    }
}

/**
* Register a stylesheet
*
* @param  string $id
* @param  string $url
* @param  array  $args
*
* @return boolean
*/
function sp_register_style($id, $url, array $args = [])
{
    return app()->asset->registerStyle($id, $url, $args);
}

/**
* Register a script
*
* @param  string $id
* @param  string $url
* @param  array  $args
*
* @return boolean
*/
function sp_register_script($id, $url, array $args = [])
{
    return app()->asset->registerScript($id, $url, $args);
}

/**
* Enqueue a stylesheet
*
* @param  string $id
* @param  array $dependencies
* @return boolean
*/
function sp_enqueue_style($id, array $dependencies = [])
{
    return app()->asset->enQueue($id, null, $dependencies);
}

/**
* Enqueue a script
*
* @param  string  $id
* @param  integer $position
* @param  array   $dependencies
* @return boolean
*/
function sp_enqueue_script($id, $position = null, array $dependencies = [])
{
    return app()->asset->enQueue($id, $position, $dependencies);
}

/**
 * Dequeue an asset
 *
 * @param  string $id
 * @return boolean
 */
function sp_dequeue_asset($id)
{
    return app()->asset->deQueue($id);
}

/**
* Check if an asset is enqueued or not
*
* @param  string  $id
* @return boolean
*/
function sp_is_enqueued($id)
{
    return app()->asset->isEnqueued($id);
}

/**
* Add a menu to the sidebar
*
* @param string $id
* @param array  $args
* @param string $insertAfter
*/
function sp_add_sidebar_menu($id, array $args, $parent = false, $insertAfter = null)
{
    return MenuStorage::addSidebarMenu($id, $args, $parent, $insertAfter);
}

/**
* Add a menu to the navbar
*
* @param string $id
* @param array  $args
* @param string $insertAfter
*/
function sp_add_navbar_menu($id, array $args, $parent = false, $insertAfter = null)
{
    return MenuStorage::addNavbarMenu($id, $args, $parent, $insertAfter);
}

/**
 * Renders the sidebar menu for dashboard
 *
 * @return string
 */
function sp_render_sidebar_menu()
{
    $menu = new SidebarGenerator(MenuStorage::getSidebarMenus());
    return $menu->renderHtml();
}

/**
 * Render dashboard tabs of menus with child elements
 *
 * @param  string $parent
 * @param  string $classes
 * @return
 */
function sp_render_tabs($parent, $classes = '')
{
    $menu = new SidebarGenerator(MenuStorage::getSidebarMenus());
    return $menu->renderTabs($parent, $classes);
}

/**
 * Renders the navbar menu for dashboard
 *
 * @return string
 */
function sp_render_navbar_menu()
{
    $menu = new NavbarGenerator(MenuStorage::getNavbarMenus());
    return $menu->renderHtml();
}

/**
 * Inserts js for dashboard dropzone fields
 *
 * @param  string $selector the dropzone selector
 * @param  string $target Optional target input selector
 * @param  array  $filetypes
 * @return string
 */
function sp_dropzone_js($selector, $target = null, array $filetypes = ['image/*'])
{
    $url = url_for('dashboard.gallery.create_post');
    $maxFileSize = format_bytes(get_max_upload_size());
    $key = registry_read('csrf_key');
    $token = registry_read('csrf_token');

    $filetypes = join(', ', $filetypes);

    $callback = '';

    if (is_string($target)) {
        $callback .= "if (response.content_url) {
          $('{$target}').val(response.content_relative_url).focus().trigger('change');
        }";
    }

    $js = <<<JS
\$('{$selector}').dropzone({
      url: "{$url}",
      maxFileSize: {$maxFileSize},
      acceptedFiles: '{$filetypes}',
      params: {
        {$key}: "{$token}",
      },
      success: function (dropzone, response) {
        {$callback}
      },
    });
JS;

    return $js;
}

/**
 * Converts flashes to bootstrap 4 alerts
 *
 * @param  string  $prefix
 * @param  boolean $dismissable
 * @param  boolean $withIcon
 * @return string
 */
function sp_alert_flashes($prefix, $dismissable = true, $withIcon = true)
{
    $types = ['danger', 'warning', 'success', 'info', 'primary', 'secondary', 'light', 'dark'];
    $flash = app()->environment['slim.flash'];
    $dismiss = '';
    if ($dismissable) {
        $dismiss = "\n" . '<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>';
    }
    $html = '';
    $extraClass = '';

    if ($withIcon) {
        $extraClass = 'alert-icon';
    }
    foreach ($types as $type) {
        $key = $prefix . '-' . $type;
        $icon = '';

        if ($withIcon) {
            $icon = sp_svg_icon_for_alert($type);
        }
        if ($flash[$key]) {
            $html .= sp_bootstrap_alert($flash[$key], $type, $icon, $dismissable) . "\n";
        }
    }

    return $html;
}

/**
 * Returns icons for sp_alert_flashes()
 *
 * @param  string $type
 * @return string
 */
function sp_svg_icon_for_alert($type)
{
    switch ($type) {
        case 'danger':
        case 'warning':
            $svgID = 'warning';
            break;

        case 'success':
            $svgID = 'checkmark';
            break;
        default:
            $svgID = 'notifications';
            break;
    }
    return svg_icon($svgID, 'mr-2');
}

/**
 * Generates an bootstrap alert
 *
 * @param  string  $message
 * @param  string  $type
 * @param  string  $iconHtml
 * @param  boolean $dismissable
 * @return string
 */
function sp_bootstrap_alert($message, $type = 'info', $iconHtml = null, $dismissable = true)
{
    $class = '';

    if ($iconHtml) {
        $class = 'alert-icon';
    }

    $dismiss = '';
    if ($dismissable) {
        $dismiss = "\n" . '<button type="button" class="close" data-dismiss="alert" aria-label="' . __('Close') . '">
    <span aria-hidden="true">&times;</span>
  </button>';
    }

    return '<div class="alert alert-' . e_attr($type) . ' ' . $class . '" role="alert">' . $dismiss .'  ' . $iconHtml . ' ' . $message . ' </div>';
}

/**
 * Die mothafuqa die
 *
 * @param  string  $msg
 * @param  integer $httpStatus
 * @return
 */
function sp_die($msg, $httpStatus = 500)
{
    $app = app();
    $body = $app->view->fetch('admin::white_screen_of_dead.php', ['error' => $msg]);
    $app->halt($httpStatus, $body);
}

/**
 * alias of sp_die()
 *
 * @return
 */
function sp_not_permitted()
{
    sp_die(__("You don't have enough permissions to perform this action."));
}

/**
 * URL to use in form for current form submit
 *
 * @param  array  $ignore
 * @return string
 */
function sp_current_form_uri(array $ignore = [])
{
    return '?' . e_attr(request_build_query($ignore, null));
}

/**
 * Insert google re-captcha markup
 *
 * @param  string  $before
 * @param  string  $after
 * @param  boolean $force
 * @param  string  $id
 * @return string
 */
function sp_google_recaptcha(
    $page = '',
    $before = null,
    $after = null,
    $force = false,
    array $data = [],
    $id = 'g-recaptcha'
) {
    $html = '';

    if (!(int) get_option('captcha_enabled') && !$force) {
        return $html;
    }

    static $captchaLocations = [];

    if (empty($captchaLocations)) {
        $captchaLocations = get_option_json('captcha_locations');
    }

    // Captcha exceptions
    if (!empty($page) && !$force) {
        if (!empty($captchaLocations[$page])) {
            return $html;
        }
    }

    if ($id == 'g-recaptcha') {
        registry_increment('g-recaptcha-ids');
        $id .= '-' . registry_read('g-recaptcha-ids');
    }

    if (is_frontend() && !isset($data['callback'])) {
        $data['callback'] = 'recaptchaSuccessCallback';
    }

    $attr = '';

    foreach ($data as $key => $value) {
        $attr .= ' data-' . e_attr($key) . '="' . e_attr($value) . '"';
    }

    $textdomain = null;

    if (is_frontend()) {
        $textdomain = _T;
    }

    $error = __('invalid-captcha', $textdomain);

    $html .= $before;
    $html .= '<div id="' . e_attr($id) . '" class="g-recaptcha" data-sitekey="' . e_attr(get_option('google_recaptcha_site_key')) . '"' . $attr . '></div>';
    $html .= $after;

    return $html;
}

/**
 * Register theme options page
 *
 * @param  string $templateName      Path to the template
 * @param  callable $handlerCallback Callback to execute when the options form is submitted
 * @param  string $label             Label for the dashboard settings sub-menu, if you leave
 *                                   this empty no menu will be added. You have to do that yourself
 * @return boolean
 */
function register_theme_options($templateName, $handlerCallback, $label = null)
{
    $theme = get_option('active_theme');
    registry_store("theme.{$theme}__options_template", $templateName, true);
    registry_store("theme.{$theme}OnOptionsSubmit", $handlerCallback);

    // To us, no label means the plugin would like to add the menu manually
    if (!$label) {
        return true;
    }

    return true;
}

/**
 * Returns if active theme has any options
 *
 * @return boolean
 */
function theme_has_options()
{
    $theme = get_option('active_theme');

    if (registry_read("theme.{$theme}__options_template")) {
        return true;
    }

    return false;
}

/**
 * Returns an array of certain theme's locales
 *
 * @param  string|null  $theme     Name of the theme, defaults to active theme
 * @param  boolean      $forceScan
 *
 * @return array
 */
function get_theme_locales($theme = null, $forceScan = false)
{
    return app()->locale->getThemeLanguages($theme, $forceScan);
}

/**
 * Access current theme URI
 *
 * @param  string $path
 * @return string
 */
function current_theme_uri($path = '')
{
    $themePath =  trailingslashit(get_option('active_theme')) . unleadingslashit($path);
    return theme_uri($themePath);
}

/**
 * Check if a theme locale is currently active
 *
 * @param  string  $locale
 * @return boolean
 */
function is_theme_active_locale($locale)
{
    return $locale === get_theme_active_locale();
}

/**
 * Get the active locale code for current theme
 *
 * @return string
 */
function get_theme_active_locale()
{
    return registry_read('_spark.theme.locale', Locale::DEFAULT_LOCALE);
}

function get_theme_active_locale_item()
{
    $locale = get_theme_active_locale();

    return app()->locale->getThemeLocaleInfo($locale);
}

/**
 * Check if a page has custom template by it's slug
 *
 * @param  string  $slug
 * @return boolean
 */
function has_custom_template($slug)
{
    $customTemplate = trailingslashit(config('custom_page_template_path', 'pages')) . $slug . '.php';

    if (has_template($customTemplate)) {
        return $customTemplate;
    }

    return false;
}

/**
 * Register a nav menu
 *
 * @param  string $location
 * @param  string $description
 * @return mixed
 */
function register_nav_menu($location, $description)
{
    $menus = (array) registry_read('__sp_nav_menus', []);
    $menus[$location] = sp_strip_tags($description);

    return registry_store('__sp_nav_menus', $menus);
}

function get_registered_nav_menus()
{
    return (array) registry_read('__sp_nav_menus', []);
}

function has_theme_menu($location)
{
    $menus = get_registered_nav_menus();
    return isset($menus[$location]);
}

/**
 * Returns the menu ID for a registered location
 *
 * @param  string $location
 * @param  mixed  $fallback
 * @return integer|null
 */
function get_active_menu_id($location, $fallback = null)
{
    return get_option("__spark__menu_id__{$location}", $fallback);
}

/**
 * Sets the menu ID for a registered location
 *
 * @param  string   $location
 * @param  integer  $menuID
 * @return integer|null
 */
function set_active_menu_id($location, $menuID)
{
    return set_option("__spark__menu_id__{$location}", $menuID);
}

/**
 * Renders a navmenu, must be registered first
 *
 * @param  mixed  $identifier
 * @param  array  $args
 * @return mixed
 */
function render_nav_menu($identifier, array $args = [])
{
    $menuGenerator = new MenuGenerator($identifier, $args);
    return $menuGenerator->renderMenu();
}

/**
 * Checks if item is loopable or not, PHP 7.1 has is_iterable for that but our min. requirement is 7.0
 *
 * @param  mixed  $item
 * @return boolean
 */
function has_items($item)
{
    if (!is_array($item)) {
        return false;
    }

    return !empty($item);
}


function sp_footer_copyright()
{
    $text = get_option('footer_copyright');

    $text = str_multi_replace($text, ['%year%' => date('Y'), '%sitename%' => get_option('site_name')]);
    return $text;
}
