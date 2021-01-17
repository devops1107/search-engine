<?php

namespace spark\drivers\Views;

use Slim\Slim;
use spark\helpers\DotArray;

/**
* Weed - A Parody of PHP Templating Engines
*
* Fuck curly braces. There, I said it. All generic PHP string replacement based templating engines suck.
* They all do. You think they don't just because they look elegant.
*
* You think pure PHP templates are verbose and hard for non coders?
* Fuck you mate. Your cached string replacement won't even be close with the speed of this thing.
* Designers can learn basic PHP if they want to build or customize a script that's powered by PHP.
* Look at WordPress, nigga. Your designers can learn TWIG, Blade,
* but can't learn a few `echo` call and `<?php if (shit): ?> <php endif ?>` statements?
*
* Fuck you, your lazy ass stupid designers and your bloated as shit multi megabyte, dozen library depended,
* regex based templating engines.
*
* What `Weed` can do?
*
* > Sections/block based rendering
* > Typical include based rendering
* > Data sharing across the templates
* > Multiple name-spaced template directories
* > Auto escape, that's right bitch.. mothafuqin autoescape
* > Dot notated access to template data
* > Ability to assign both variables and template data
* > Pure PHP, no string replacement no caching, no bullshit
* > Zero dependencies
*
*
*/
class Weed implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * Base Weed Namespace
     */
    const DEFAULT_NAMESPACE = '___weed_base_templates';

    /**
     * Template var name
     */
    const TEMPLATE_VAR_NAME = 't';

    /**
     * Path(s) to the templates
     *
     * @var array
     */
    protected $templatesPath = [];

    /**
     * Parser options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Template Data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Current template path
     *
     * @var string
     */
    protected $nowRendering;

    /**
     * Current(ly rendering) template's namespace
     *
     * @var string
     */
    protected $currentNamespace;

    /**
     * Registered filters
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Registered sections
     *
     * @var array
     */
    protected $sections = [];

    /**
     * Registered global variables
     *
     * @var array
     */
    protected $vars = [];

    /**
     * Started section
     *
     * @var string
     */
    protected $startedSection;

    /**
     * Light up a new joint
     *
     * @param string $templatesPath Path to the base template directory
     * @param array  $options       Array of options
     */
    public function __construct($templatesPath, array $options = [])
    {
        $this->templatesPath[static::DEFAULT_NAMESPACE] = rtrim($templatesPath, '\\//');

        $defaults = [
            'autoEscape' => true
        ];

        $this->options = array_merge($defaults, $options);
        $this->registerFilter('e', [$this, 'escape']);
        $this->registerFilter('e_attr', [$this, 'escapeHtmlAttribute']);
    }

    /**
     * Add data to set
     *
     * @param array $items Key-value array of data to append to this set
     */
    public function setVars(array $items)
    {
        $this->vars = array_merge($this->vars, $items);
        return $this;
    }

    /**
     * Alias of self::render()
     *
     * @param  string  $templateName
     * @param  array   $data
     */
    public function extend($templateName, array $data = [])
    {
        return $this->render($templateName, $data);
    }

    /**
     * Start recording a section
     *
     * @param  string $name The unique section name
     * @return
     */
    public function start($name)
    {
        // End any existing section recording instance first
        $this->end();

        ob_start();
        $this->startedSection = $name;
    }

    /**
     * End recording a section, self::start() must be called before!
     *
     * @return
     */
    public function end($appendMode = 1)
    {
        if ($this->startedSection && ob_get_level()) {
            if (!isset($this->sections[$this->startedSection])) {
                $this->sections[$this->startedSection] = null;
            }

            $content = ob_get_clean();

            if ($appendMode === 1) {
                $this->sections[$this->startedSection] .= $content;
            } elseif ($appendMode === -1) {
                $this->sections[$this->startedSection] = $this->sections[$this->startedSection] . $content;
            } else {
                $this->sections[$this->startedSection] = ob_get_clean();
            }

            $this->startedSection = null;
        }
    }

    /**
     * Output recorded section
     *
     * @param  string $name           The unique section name
     * @param  string $emptyFallback Fallback text to display if section is empty
     *                                (Optional)
     * @return
     */
    public function section($name, $emptyFallback = '')
    {
        if (isset($this->sections[$name])) {
            echo $this->sections[$name];
        } else {
            echo $emptyFallback;
        }
    }

    /**
     * Register a filter to use
     *
     * @param  string $name     Name
     * @param  callable $callback Callable array, string or closure
     * @return Weed
     */
    public function registerFilter($name, $callback)
    {
        $this->filters[$name] = $callback;
        return $this;
    }

    /**
     * Add a new template folder for grouping templates under different namespaces.
     *
     * @param  string  $namespace
     * @param  string  $templatesPath
     * @return Weed
     */
    public function addFolder($namespace, $templatesPath)
    {
        if ($this->hasFolder($namespace)) {
            return false;
        }

        $this->templatesPath[$namespace] = $templatesPath;
        return true;
    }

    public function hasFolder($namespace)
    {
        return isset($this->templatesPath[$namespace]);
    }

    /**
     * Remove a template folder by it's namespace.
     *
     * @param  string $namespace
     * @return Weed
     */
    public function removeFolder($namespace)
    {
        unset($this->templatesPath[$namespace]);
    }

    /**
     * Renders a template
     *
     * @param  string $templateName The template file name
     * @param  array  $data         Template data
     * @return string
     */
    public function render($templateName, array $data = [])
    {
        $pathInfo = $this->buildTemplatePath($templateName);
        $this->nowRendering = $pathInfo['path'];
        $this->currentNamespace = $pathInfo['namespace'];

        if (!is_file($this->nowRendering)) {
            throw new \LogicException("No template found at: {$this->nowRendering}");
        }

        $this->replace($data);
        unset($templateName, $data);
        extract($this->vars);
        ${static::TEMPLATE_VAR_NAME} = $this;

        ob_start();
        include $this->nowRendering;
        $parsed = ob_get_clean();

        return $parsed;
    }

    /**
     * Check if a template exists or not
     *
     * @param  string $templateName
     * @return boolean
     */
    public function exists($templateName)
    {
        $pathInfo = $this->buildTemplatePath($templateName);
        return (bool) @is_file($pathInfo['path']);
    }

    /**
     * Alias of self::render()
     *
     * @param  string $templateName The template file name
     * @param  array  $data         Template data
     * @return string
     */
    public function insert($templateName, array $data = [])
    {
        return $this->render($templateName, $data);
    }

    /**
     * Build the template path
     *
     * @param  string $templateName The template file name
     * @return array
     */
    protected function buildTemplatePath($templateName)
    {
        $namespaceSlices = explode('::', $templateName);
        $namespace = static::DEFAULT_NAMESPACE;

        if (isset($namespaceSlices[1])) {
            $namespace = $namespaceSlices[0];
            $templateName = $namespaceSlices[1];
        }

        if (!isset($this->templatesPath[$namespace])) {
            throw new \RuntimeException("Namespace {$namespace} is not registered!");
        }

        return [
            'path' => $this->templatesPath[$namespace] . '/' . $templateName,
            'namespace' => $namespace
        ];
    }

    /**
     * Escape string.
     *
     * @param  string      $string
     * @param  null|string $functions
     * @return string
     */
    public function escape($string, $functions = null, $doubleEncode = true)
    {
        static $flags;

        if (!isset($flags)) {
            $flags = ENT_QUOTES | (defined('ENT_SUBSTITUTE') ? ENT_SUBSTITUTE : 0);
        }

        if ($functions) {
            $string = $this->batch($string, $functions);
        }

        return htmlspecialchars($string, $flags, 'UTF-8', $doubleEncode);
    }

    /**
     * Alias of self::escape()
     *
     * @param  string $string
     * @return string
     */
    public function eAttr($string, $functions = null)
    {
        return $this->escape($string, $functions, false);
    }

    /**
     * Alias to escape function.
     *
     * @param  string      $string
     * @param  null|string $functions
     * @return string
     */
    public function e($string, $functions = null)
    {
        return $this->escape($string, $functions);
    }

    /**
     * Apply multiple functions to variable.
     *
     * @param  mixed  $var
     * @param  string $functions
     * @return mixed
     */
    public function batch($var, $functions)
    {
        foreach (explode('|', $functions) as $function) {
            if (isset($this->filters[$function])) {
                $var = call_user_func($this->filters[$function], $var);
            } elseif (is_callable($function)) {
                $var = call_user_func($function, $var);
            } else {
                throw new \LogicException(
                    'The batch function could not find the "' . $function . '" function.'
                );
            }
        }

        return $var;
    }

    /**
     * Dynamically escapes var when echoing based on the options
     *
     * @param  mixed $input Input variable
     * @return mixed
     */
    protected function dynamicEscape($input)
    {
        if ($this->options['autoEscape'] && is_string($input)) {
            return $this->escape($input);
        }

        return $input;
    }

    /**
     * Set data key to value
     *
     * @param string $key   The data key
     * @param mixed  $value The data value
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Get data value with key
     *
     * @param  string $key     The data key
     * @param  mixed  $default The value to return if data key does not exist
     * @return mixed           The data value, or the default value
     */
    public function get($key, $default = null)
    {
        if (!$this->has($key)) {
            return $default;
        }

        return $this->data[$key];
    }

    /**
     * Add data to set
     *
     * @param array $items Key-value array of data to append to this set
     */
    public function replace(array $items)
    {
        $this->data = array_merge($this->data, $items);
    }

    /**
     * Fetch set data
     *
     * @return array This set's key-value data array
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * Fetch set data keys
     *
     * @return array This set's key-value data array keys
     */
    public function keys()
    {
        return array_keys($this->data);
    }

    /**
     * Does this set contain a key?
     *
     * @param  string  $key The data key
     * @return boolean
     */
    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Remove value with key from this set
     *
     * @param  string $key The data key
     */
    public function remove($key)
    {
        unset($this->data[$key]);
    }

    /**
     * Clear all values
     */
    public function clear()
    {
        $this->data = [];
    }

    /**
     * Array Access
     */

    public function offsetExists($offset)
    {
        return DotArray::exists($this->data, $offset);
    }

    public function offsetGet($offset)
    {
        $var = DotArray::get($this->data, $offset);
        return $this->dynamicEscape($var);
    }

    public function offsetSet($offset, $value)
    {
        DotArray::set($this->data, $offset, $value);
    }

    public function offsetUnset($offset)
    {
        DotArray::delete($this->data, $offset);
    }

    /**
     * Countable
     */

    public function count()
    {
        return count($this->data);
    }

    /**
     * IteratorAggregate
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }
}
