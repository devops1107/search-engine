<?php

namespace spark\drivers\Asset;

/**
* AssetManager
*
* @package spark
*/
class AssetManager
{
    /**
     * Position label for header assets
     *
     * @var string
     */
    const POSITION_HEADER = 1;

    /**
     * Position label for footer assets
     *
     * @var string
     */
    const POSITION_FOOTER = 2;

    /**
     * Type for stylesheets
     *
     * @var string
     */
    const TYPE_STYLE = 'text/css';

    /**
     * Type for scripts
     *
     * @var string
     */
    const TYPE_SCRIPT = 'text/javascript';

    /**
     * Assets
     *
     * @var array
     */
    protected $assets = [];

    public function __construct()
    {
    }

    /**
     * Register asset
     *
     * @param  string $id
     * @param  array  $params
     * @return boolean
     */
    protected function register($id, array $params)
    {
        $this->assets[$id] = $params;
        return true;
    }

    /**
     * Deregister asset
     *
     * @param  string $id
     * @return boolean
     */
    public function deregister($id)
    {
        if (isset($this->assets[$id])) {
            unset($this->assets[$id]);
            return true;
        }

        return false;
    }

    /**
     * Alias of self::register() to register a stylesheet
     *
     * @param  string $id
     * @param  string $url
     * @param  array  $args
     *
     * @return boolean
     */
    public function registerStyle($id, $url, array $args = [])
    {
        $params = array_merge(static::getAssetLayout(), $args);
        $params['position'] = static::POSITION_HEADER;
        $params['url'] = $url;
        $params['type'] = static::TYPE_STYLE;
        $params['enqueued'] = false;

        return $this->register($id, $params);
    }

    /**
     * Alias of self::register() to register a script
     *
     * @param  string $id
     * @param  string $url
     * @param  array  $args
     *
     * @return boolean
     */
    public function registerScript($id, $url, array $args = [])
    {
        $params = array_merge(static::getAssetLayout(), $args);
        $params['url'] = $url;
        $params['type'] = static::TYPE_SCRIPT;

        $params['enqueued'] = false;

        return $this->register($id, $params);
    }

    /**
     * Enqueue an asset
     *
     * @param  string $id
     * @param  mixed $position
     * @param  array $dependencies
     * @return boolean
     */
    public function enQueue($id, $position = null, array $dependencies = [])
    {
        if (isset($this->assets[$id])) {
            $this->assets[$id]['enqueued'] = true;
            $this->assets[$id]['dependencies'] = $dependencies;
            if ($position) {
                $this->assets[$id]['position'] = $position;
            }

            return true;
        }

        return false;
    }

    /**
     * Dequeue a script
     *
     * @param  string $id
     * @return boolean
     */
    public function deQueue($id)
    {
        if (isset($this->assets[$id])) {
            $this->assets[$id]['enqueued'] = false;
            return true;
        }

        return false;
    }

    /**
     * Returns all assets
     *
     * @return array
     */
    public function getAssets()
    {
        return $this->assets;
    }

    /**
     * Check if the asset is registered on the AssetManager or not
     *
     * @param  string  $id
     * @return boolean
     */
    public function hasAsset($id)
    {
        if (!isset($this->assets[$id])) {
            return false;
        }

        return true;
    }

    /**
     * Check if an asset is enqueued or not
     *
     * @param  string  $id
     * @return boolean
     */
    public function isEnqueued($id)
    {
        return isset($this->assets[$id]['enqueued']) && $this->assets[$id]['enqueued'] === true;
    }


    /**
     * Provides base asset layout
     *
     * @return array
     */
    public static function getAssetLayout()
    {
        return [
            'url'      => '',
            'type'     => static::TYPE_STYLE,
            'abspath'  => '',
            'version'  => APP_VERSION,
            'position' => static::POSITION_HEADER,
            'defer'    => false,
            'async'    => false,
            'media'    => null,
            'enqueued' => false,
            'rendered' => false,
            'dependencies' => [],
        ];
    }

    public function renderHtml($position = null)
    {
        if ($position === static::POSITION_FOOTER) {
            $position = static::POSITION_FOOTER;
        } else {
            $position = static::POSITION_HEADER;
        }

        $assets = [];

        // Get enqueued assets for current position
        foreach ($this->getAssets() as $id => $asset) {
            // this isn't working anymore
            if ($asset['position'] !== $position || !$asset['enqueued']) {
                continue;
            }

            $assets[$id] = $asset;
        }


        list($original, $sorted) = [$assets, []];

        while (count($assets) > 0) {
            foreach ($assets as $asset => $value) {
                $this->evaluateAsset($asset, $value, $original, $sorted, $assets);
            }
        }


        $html = '';

        $i = 0;

        foreach ($sorted as $id => $asset) {
            $i++;

            if ($i > 1) {
                $html .= "\t";
            }

            $html  .= $this->renderSingle($id, $asset);
        }

        return $html;
    }


    /**
     * Evaluate an asset and its dependencies.
     *
     * @param  string  $asset
     * @param  array  $value
     * @param  array  $original
     * @param  array  $sorted
     * @param  array  $assets
     *
     * @return void
     */
    protected function evaluateAsset(
        $asset,
        array $value,
        array $original,
        array &$sorted,
        array &$assets
    ) {
        // If the asset has no more dependencies, we can add it to the sorted
        // list and remove it from the array of assets. Otherwise, we will
        // not verify the asset's dependencies and determine if they've been
        // sorted.
        if (count($assets[$asset]['dependencies']) == 0) {
            $sorted[$asset] = $value;

            unset($assets[$asset]);
        } else {
            $this->evaluateAssetWithDependencies($asset, $original, $sorted, $assets);
        }
    }

    /**
     * Evaluate an asset with dependencies.
     *
     * @param  string  $asset
     * @param  array   $original
     * @param  array   $sorted
     * @param  array   $assets
     *
     * @return void
     */
    protected function evaluateAssetWithDependencies(
        $asset,
        array $original,
        array &$sorted,
        array &$assets
    ) {
        foreach ($assets[$asset]['dependencies'] as $key => $dependency) {
            if (! $this->dependencyIsValid($asset, $dependency, $original, $assets)) {
                unset($assets[$asset]['dependencies'][$key]);

                continue;
            }

            // If the dependency has not yet been added to the sorted
            // list, we can not remove it from this asset's array of
            // dependencies. We'll try again onthe next trip through the
            // loop.
            if (isset($sorted[$dependency])) {
                unset($assets[$asset]['dependencies'][$key]);
            }
        }
    }

    /**
     * Verify that an asset's dependency is valid.
     *
     * A dependency is considered valid if it exists, is not a circular
     * reference, and is not a reference to the owning asset itself. If the
     * dependency doesn't exist, no error or warning will be given. For the
     * other cases, an exception is thrown.
     *
     * @param  string  $asset
     * @param  string  $dependency
     * @param  array   $original
     * @param  array   $assets
     *
     * @throws \RuntimeException
     *
     * @return bool
     */
    protected function dependencyIsValid(
        $asset,
        $dependency,
        array $original,
        array $assets
    ) {
        // Determine if asset and dependency is circular.
        $isCircular = function ($asset, $dependency, $assets) {
            return isset($assets[$dependency]) && in_array($asset, $assets[$dependency]['dependencies']);
        };

        if (!isset($original[$dependency])) {
            return false;
        } elseif ($dependency === $asset) {
            throw new \RuntimeException("Asset [$asset] is dependent on itself.");
        } elseif ($isCircular($asset, $dependency, $assets)) {
            throw new \RuntimeException("Assets [$asset] and [$dependency] have a circular dependency.");
        }

        return true;
    }

    /**
     * Renders a single asset into html tag
     *
     * @param  string $id
     * @param  array  $asset
     * @return string
     */
    protected function renderSingle($id, array $asset)
    {
        if ($this->assets[$id]['rendered']) {
            return '';
        }

        $id = e_attr($id);
        $asset['url'] = e_attr($asset['url']);
        $asset['version'] = e_attr($asset['version']);

        if (is_string($asset['abspath']) && is_file($asset['abspath']) && is_dev()) {
            $asset['version'] = (int) @filemtime($asset['abspath']);
        }

        // If it reached here, it'll be considered rendered
        $this->assets[$id]['rendered'] = true;
        $assetURL = $asset['url'];
        // If a query string starter is already present we add the version parameter by appending it
        if (strpos($assetURL, '?') !== false) {
            $assetURL .= "&v={$asset['version']}";
        } else {
            // otherwise let this be a start
            $assetURL .= "?v={$asset['version']}";
        }

        if ($asset['type'] === static::TYPE_STYLE) {
            $media = null;

            if ($asset['media']) {
                $media = ' media="' . e_attr($asset['media']) . '"';
            }
            $html = '<link rel="stylesheet" id="' . $id . '" type="text/css" href="' . $assetURL . '"'. $media . '>'
            . "\n";
            return $html;
        }

        // Handle scripts
        if ($asset['type'] === static::TYPE_SCRIPT) {
            $extra = null;
            if ($asset['async']) {
                $extra = ' async';
            }

            if ($asset['defer']) {
                $extra .= ' defer';
            }

            $html = '<script id="' . $id . '" type="text/javascript" src="' . $assetURL . '"'. $extra . '></script>'
            . "\n";
            return $html;
        }

        return '';
    }
}
