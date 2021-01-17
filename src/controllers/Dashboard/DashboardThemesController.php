<?php

namespace spark\controllers\Dashboard;

use spark\controllers\Controller;
use spark\drivers\Nav\ArrayPagination;
use spark\drivers\Nav\Pagination;
use PclZip;

/**
* DashboardThemesController
*
* @package spark
*/
class DashboardThemesController extends DashboardController
{
    public function __construct()
    {
        parent::__construct();

        // this is it
        if (!current_user_can('manage_themes')) {
            sp_not_permitted();
        }

        breadcrumb_add('dashboard.appearance', __('Appearance'), '#appearance');
        breadcrumb_add('dashboard.themes', __('Themes'), url_for('dashboard.themes'));

        view_set('themes__active', 'active');
    }

    /**
     * List entries
     *
     * @return
     */
    public function index()
    {
        $app = app();

        // Current page number
        $currentPage = (int) $app->request->get('page', 1);

        // Items per page
        $itemsPerPage = (int) $app->config('dashboard.items_per_page');

        // Total item count
        $totalCount = $app->theme->getThemesCount();

        $queryStr = request_build_query(['page']);

        $arrayPagination = new ArrayPagination;

        // Pagination instance
        $pagination = new Pagination($totalCount, $currentPage, $itemsPerPage);
        $pagination->setUrl("?page=@id@{$queryStr}");

        // Generated HTML
        $paginationHtml = $pagination->renderHtml();

        // Offset value based on current page
        $offset = $pagination->offset();

        $themes = $app->theme->listThemes();

        // List entries
        $entries = $arrayPagination->generate($themes, $currentPage, $itemsPerPage);

        // Template data
        $data = [
            'list_entries'    => $entries,
            'total_items'     => $totalCount,
            'offset'          => $offset === 0 ? 1 : $offset,
            'current_page'    => $currentPage,
            'items_per_page'  => $itemsPerPage,
            'current_items'   => $itemsPerPage * $currentPage,
            'pagination_html' => $paginationHtml,
            'query_str'       => $queryStr
        ];
        return view('admin::themes/index.php', $data);
    }

    /**
     * Add new theme page
     *
     * @return
     */
    public function create()
    {
        // Set breadcrumb trails
        breadcrumb_add('dashboard.themes.create', __('Add New Theme'));

        $data = [];
        return view('admin::themes/create.php', $data);
    }

    /**
     * Process uploaded theme
     *
     * @return
     */
    public function createPOST()
    {
        if (is_demo()) {
            flash('themes-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.themes');
        }

        if (empty($_FILES['theme_archive'])) {
            flash('themes-danger', __('Please select a file!'));
            return redirect_to_current_route();
        }

        $archive = $_FILES['theme_archive'];

        // Ensure it's a ZIP file
        if ($archive['type'] !== 'application/zip') {
            flash('themes-danger', __('Theme must be a ZIP archive!'));
            return redirect_to_current_route();
        }

        // Where to put our beloved theme
        $themesDir = themespath();

        // Assume the archive name as theme folder/key name at first
        $themeName = pathinfo($archive['name'], PATHINFO_FILENAME);

        // Fuck, this library is so old
        $zip = new \PclZip($archive['tmp_name']);

        $tempDir = srcpath("var/tmp/" . md5($themeName));
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $files = $zip->extract(PCLZIP_OPT_PATH, $tempDir, PCLZIP_OPT_REPLACE_NEWER);

        if (!$files) {
            rrmdir($tempDir);
            flash('themes-danger', __('Unknown Error Occured. Possibly Corrupted ZIP file.'));
            return redirect_to_current_route();
        }

        $tempFiles = glob(trailingslashit($tempDir) . '*');

        $foundthemes = 0;

        foreach ($tempFiles as $file) {
            // No files are allowed in the root of theme archive
            if (is_file($file)) {
                continue;
            }

            $theme = basename($file);
            $themePath = trailingslashit($file) . 'skin.php';
            // so we're talking to a directory? fine
            // let's check if the directory contains a standard theme file or not
            if (!file_exists($themePath)) {
                // you don't matter to us
                continue;
            }

            // so, we're talking to a standard theme file?
            // we'll find out soon enough
            $data = get_file_data($themePath, ['name' => 'Theme Name']);

            if (empty(trim($data['name']))) {
                // come on! seriously? after all this you don't even have a name?
                // and we thought we're something special </3
                continue;
            }

            // yahoooo! we found our soul mate
            rmove($file, themespath($theme));
            $foundthemes++;
        }

        rrmdir($tempDir);

        if ($foundthemes) {
            flash('themes-success', sprintf(__('%d theme(s) were added successfully'), $foundthemes));
            return redirect_to('dashboard.themes');
        }

        flash('themes-danger', __("No valid themes found in the archive."));
        return redirect_to_current_route();
    }

    /**
     * Applies  theme
     *
     * @param  string $theme
     * @return
     */
    public function applyTheme($theme)
    {
        if (is_demo()) {
            flash('themes-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.themes');
        }

        $themeManager = app()->theme;

        if (!$themeManager->themeExists($theme)) {
            flash('themes-danger', __('No such theme exists on the disk'));
        } elseif ($themeManager->isActive($theme)) {
            flash('themes-warning', sprintf(__('Theme %s is already actived!'), sp_strip_tags($theme)));
        } else {
            set_option('active_theme', $theme);
            flash('themes-success', __('Theme was activated successfully'));

            $themeManager->loadTheme($theme);
        }

        return redirect_to('dashboard.themes');
    }

    /**
     * Delete entry page
     *
     * @param string $theme
     * @return
     */
    public function delete($theme)
    {
        // Set breadcrumb trails
        breadcrumb_add('dashboard.themes.delete', __('Delete Theme'));

        $themeManager = app()->theme;

        if (!$themeManager->themeExists($theme)) {
            flash('themes-danger', __('No such theme exists on the disk'));
            return redirect_to('dashboard.themes');
        }

        if ($themeManager->isActive($theme)) {
            flash('themes-warning', sprintf(__('Theme %s is currently active!'), sp_strip_tags($theme)));
            return redirect_to('dashboard.themes');
        }

        $meta = $themeManager->getThemeMeta($theme);

        $data = [
            'theme' => $theme,
            'meta' => $meta
        ];
        return view('admin::themes/delete.php', $data);
    }

    /**
     * Performs theme deletion
     *
     * @param string $theme
     * @return
     */
    public function deletePOST($theme)
    {
        if (is_demo()) {
            flash('themes-info', $GLOBALS['_SPARK_I18N']['demo_mode']);

            if (is_ajax()) {
                return;
            }

            return redirect_to('dashboard.themes');
        }

        $themeManager = app()->theme;

        if (!$themeManager->themeExists($theme)) {
            flash('themes-danger', __('No such theme exists on the disk'));
        } elseif ($themeManager->isActive($theme)) {
            flash('themes-warning', sprintf(__('Theme %s is currently active!'), sp_strip_tags($theme)));
        } else {
            $themeManager->loadTheme($theme);
            rrmdir(themespath($theme));
            flash('themes-success', __('Theme was deleted successfully'));
        }

        if (is_ajax()) {
            return;
        }

        return redirect_to('dashboard.themes');
    }
}
