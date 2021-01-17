<?php

namespace spark\drivers\Views;

/**
* ThemeManager
*
* @package spark
*/
class ThemeManager
{
    /**
     * Stores active theme meta
     *
     * @var array
     */
    protected $themeMeta = [];

    /**
     * Stores all scanned themes
     *
     * @var array
     */
    protected $themeList;

    /**
     * Init. the theme engine
     *
     * @return
     */
    public function initEngine()
    {
        $app = app();
        $currentTheme = $app->options->get('active_theme');
        $currentThemePath = themespath($currentTheme);

        $themeViewsPath = trailingslashit($currentThemePath) . 'views';

        if (!$this->themeExists($currentTheme)) {
            throw new \RuntimeException("Active theme directory doesn't contain any skin file!");
        }

        $this->themeMeta = $this->getThemeMeta($currentTheme);

        // Set default view path to active theme's views directory
        $app->config('templates.path', $themeViewsPath);

        // Make WeedView our template engine
        $weed = new WeedView;
        $weed->setTemplatesDirectory($themeViewsPath);
        $weed->addFolder('admin', srcpath('views'));
        $app->view($weed);
    }

    /**
     * Returns active theme's meta
     *
     * @param  string  $key
     * @param  boolean $fallback
     *
     * @return mixed
     */
    public function meta($key, $fallback = false)
    {
        return isset($this->themeMeta[$key]) ? $this->themeMeta[$key] : $fallback;
    }

    /**
     * Builds path to a theme's meta file
     *
     * @param  string $theme
     * @return string
     */
    public function buildThemeMetaFilePath($theme)
    {
        return themespath("{$theme}/skin.php");
    }

    /**
     * Parses and returns theme meta-data
     *
     * @param  string $theme
     * @return array
     */
    public function getThemeMeta($theme)
    {
        $defaultMeta = [
            'name' => 'Theme Name',
            'uri' => 'Theme URI',
            'version' => 'Version',
            'description' => 'Description',
            'author' => 'Author',
            'author_uri' => 'Author URI',
        ];

        $themeFile = $this->buildThemeMetaFilePath($theme);
        $metaData   = get_file_data($themeFile, $defaultMeta);

        if (empty($metaData['name'])) {
            $metaData['name'] = $theme;
        }

        return $metaData;
    }

    public function loadTheme($theme)
    {
        require_once $this->buildThemeMetaFilePath($theme);
    }

    /**
     * List all plugins
     *
     * @return array
     */
    public function listThemes()
    {
        // sorry but we'll scan only once!
        if (is_array($this->themeList)) {
            return $this->themeList;
        }

        // Set plugin list as array
        $this->themeList = [];

        // Glob the folders
        $folders = @glob(trailingslashit(basepath(THEME_DIR)) . "*", GLOB_ONLYDIR);

        // Nothing? fine
        if (empty($folders)) {
            return $this->themeList;
        }


        foreach ($folders as $themeDir) {
            $theme = basename($themeDir);

            // no theme file? fuck off
            if (!$this->themeExists($theme)) {
                continue;
            }

            // Build the data
            $data = [
                'key'    => $theme,
                'active' => false,
                'icon'   => site_uri("assets/img/default.png"),
                'meta'   => []
            ];

            // mark theme as activated
            if ($this->isActive($theme)) {
                $data['active'] = true;
            }

            if (is_file(themespath("{$theme}/icon.png"))) {
                $data['icon'] = theme_uri("{$theme}/icon.png");
            }

            $data['meta'] = $this->getThemeMeta($theme);

            $this->themeList[$theme] = $data;
        }

        return $this->themeList;
    }


    /**
     * Get total themes count
     *
     * @return integer
     */
    public function getThemesCount()
    {
        $this->listThemes();

        return count($this->themeList);
    }

    /**
     * Check if a theme exists on the disk
     *
     * @param  string $theme
     * @return boolean
     */
    public function themeExists($theme)
    {
        return @is_file($this->buildThemeMetaFilePath($theme));
    }

    /**
     * Returns if a theme is currently active or not
     *
     * @param  string  $theme
     * @return boolean
     */
    public function isActive($theme)
    {
        $app = app();
        return $app->options->get('active_theme') === $theme;
    }
}
