<?php

use Slim\Slim;

/**
 * The Core Functions required by the app
 *
 * All these functions are standalone and only depends on Core CONSTANTS
 */

/*----------------------------------------
*   APP STATE RELATED FUNCTIONS
*
* ----------------------------------------
*/

/**
 * Determines if the current version of PHP is equal to or greater than the supplied value
 *
 * @param   string
 * @return  bool    TRUE if the current version is $version or higher
 */
function is_php($version)
{
    static $_is_php;
    $version = (string)$version;

    if (!isset($_is_php[$version])) {
        $_is_php[$version] = version_compare(PHP_VERSION, $version, '>=');
    }

    return $_is_php[$version];
}

/**
 * Returns if dev mode is enabled or not
 *
 * @return boolean
 */
function is_dev()
{
    return defined('DEV_MODE') && DEV_MODE === true;
}

/**
 * Returns if demo mode is enabled or not
 *
 * @return boolean
 */
function is_demo()
{
    return defined('DEMO_MODE') && DEMO_MODE === true;
}

/**
 * Returns if the app is installed or not
 *
 * @return boolean
 */
function is_installed()
{
    static $installed = null;
    if (is_null($installed)) {
        $installed = (bool) is_file(srcpath('config/db.php'));
    }

    return $installed;
}

/*----------------------------------------
*   PATH & DIRECTORY RELATED FUNCTIONS
*
* ----------------------------------------
*/

/**
 * Get absolute path string
 *
 * @param  string  $path The path
 * @param  string|boolean  $base Custom Base path, BASEPATH is used as default
 * @return string
 */
function basepath($path = '', $base = null)
{
    // If base is not defined use BASEPATH as base path
    if (!$base) {
        $base = BASEPATH;
    }

    $base = unixpath($base);
    $path = unixpath($path);

    if (empty($path) || $path === '/') {
        return untrailingslashit($base) . $path;
    }

    $basepath = $base . unleadingslashit($path);
    return $basepath;
}

/**
 * Alias of basepath() uses SRCPATH as base directory
 *
 * @param  string  $path The path
 * @return string
 */
function srcpath($path = '')
{
    return basepath($path, SRCPATH);
}

/**
 * Alias of basepath() uses BASEPATH and SITE_DIR as base directory
 *
 * @param  string  $path The path
 * @return string
 */
function sitepath($path = '')
{
    return basepath($path, trailingslashit(BASEPATH) . trailingslashit(SITE_DIR));
}

/**
 * Alias of basepath() uses BASEPATH and THEME_DIR as base directory
 *
 * @param  string  $path The path
 * @return string
 */
function themespath($path = '')
{
    return basepath($path, trailingslashit(BASEPATH) . trailingslashit(THEME_DIR));
}


/**
 * Alias of basepath() uses uploads as base directory
 *
 * @param  string  $path The path
 * @return string
 */
function uploadspath($path = '')
{
    $path = trailingslashit(UPLOADS_DIR) . unleadingslashit($path);
    return basepath($path, BASEPATH);
}


/**
 * Load bunch of function files from the src/functions directory
 *
 * @param  array   $files Array of file names
 * @param  boolean $once  Include once or multiple times?
 * @return
 */
function load_functions(array $files, $once = true)
{
    foreach ($files as $file) {
        if ($once) {
            include_once srcpath("functions/{$file}");
        } else {
            include srcpath("functions/{$file}");
        }
    }
}

/*----------------------------------------
*   STRING RELATED FUNCTIONS
*
* ----------------------------------------
*/
/**
 * Format path string to unix style
 *
 * @param  string $windowsPath Path to convert
 * @return string
 */
function unixpath($windowsPath)
{
    return str_replace('\\', '/', $windowsPath);
}

/**
 * Add slash to end of a string
 *
 * @param  string $string The string
 * @param  string $slash     The trailing slash to add, defaults to: '/'
 * @return string
 */
function trailingslashit($string, $slash = '/')
{
    return untrailingslashit($string) . $slash;
}

/**
 * Remove trailing slashes
 *
 * @param  string $string The string
 * @return string
 */
function untrailingslashit($string, $slashes = '/\\')
{
    return rtrim($string, $slashes);
}

/**
 * Add leading slash to a string
 *
 * @param  string $string The string
 * @param  string $slash     The trailing slash to add, defaults to: '/'
 * @return string
 */
function leadingslashit($string, $slash = '/')
{
    return $slash . unleadingslashit($string);
}

/**
 * Remove leading slashe
 *
 * @param  string $string The string
 * @return string
 */
function unleadingslashit($string, $slashes = '/\\')
{
    return ltrim($string, $slashes);
}

/**
 * Trim slashes from both end of the string
 *
 * @param  string $string The string
 * @param  string $slash  The trailing slash to trim, defaults to: '/\\'
 * @return string
 */
function unslashit($string, $slashes = '/\\')
{
    return trim($string, $slashes);
}

/**
 * Add slashes to both end of the string
 *
 * @param  string $string The string
 * @param  string $slash  The trailing slash to add, defaults to: '/'
 * @return string
 */
function doslashit($string, $slash = '/')
{
    return $slash . unslashit($string) . $slash;
}

/**
 * Pascal case a hyphen/underscored string
 *
 * @param  string  $string
 * @param  boolean $hyphens
 * @param  boolean $underscores
 * @return string
 */
function pascal_case_it($string, $hyphens = true, $underscores = true)
{
    // wipe out any mothafuckin spaces
    $string = preg_replace('/\s+/', '', $string);

    // explode by hyphens
    if ($hyphens) {
        $string = explode('-', $string);
        $string = array_map('ucfirst', $string);
        $string = join($string, '');
    }

    // Now by underscores
    if ($underscores) {
        $string = explode('_', $string);
        $string = array_map('ucfirst', $string);
        $string = join($string, '');
    }

    return $string;
}

/**
 * Encode string to URL safe base64 encoded data
 *
 * @param  string $input
 * @return string
 */
function base64_url_encode($input)
{
    return strtr(base64_encode($input), '+/=', '-_,');
}

/**
 * Decode URL safe base64 encoded data
 *
 * @param  string $input
 * @return string
 */
function base64_url_decode($input)
{
    return base64_decode(strtr($input, '-_,', '+/='));
}

/**
 * Performs string templating
 *
 * @param  string $string String, eg. Hello {{ user }}
 * @param  array  $params Replacement as key => value
 * @return string
 */
function strtpl($string, array $params)
{
    // Racism alert! String only!
    if (!is_string($string)) {
        throw new InvalidArgumentException(__FUNCTION__ . " expects parameter \$string to be string, " . gettype($string) . " given!");
    }

    $string = preg_replace_callback('/{{(\s*?[\w\.]++\s*?)}}/m', function ($match) use ($params) {
        // Grab the token, and remove all leading and trailing spaces
        $token = trim($match[1]);
        // Make sure we ignore blank tokens
        if (empty($token)) {
            return $match[0];
        }
        if (!array_key_exists($token, $params)) {
            return $match[0];
        }
        return $params[$token];
    }, $string);
    return $string;
}

/**
 * Perform case insensitive string replacement to string from an array with `token => replacement` format
 *
 * @param  string $string
 * @param  array  $replacements
 * @return string
 */
function str_multi_replace($string, array $replacements)
{
    return str_ireplace(array_keys($replacements), array_values($replacements), $string);
}


/**
 *
 *
 * @param int $len
 * @return string|null
 */

function getCookieName($len = 10) {
    $charRange = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789!@#$%^&*()";
    $pw = null;
    $length = strlen($charRange);
    for ($x = 0; $x < $len; $x++) {
        $n = rand(0, $length);
        $pw .= $charRange[$n];
    }
    return $pw;
}

/**
 * Get a random string. Not secure! Just a random string!
 *
 * @param integer $length The length of the string
 * @param string  $keyspace  The characters to use, Default is: 0-9-A-z
 * @return string
 */


function str_random($length = 10, $keyspace = null)
{
    if (is_null($keyspace)) {
        $keyspace = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTWXYZ0123456789@%&';
    }

    $random = "rand";

    if (function_exists('random_int')) {
        $random = "random_int";
    } elseif (function_exists('mt_rand')) {
        $random = "mt_rand";
    }

    $length = (int) $length;
    $str = '';

    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $k = $random(0, $max);
        $str .= $keyspace[$k];
    }
    return $str;
}

/**
 * Generate a cryptographically secure random token
 *
 * @param integer $length The length of bytes
 *                        Returned string will be 2x of the string length provided
 *                        As it will be converted to hex
 * @return string
 */
function str_random_secure($length = 20)
{
    // for PHP7 we have another great solution
    if (function_exists('random_bytes')) {
        return bin2hex(random_bytes($length));
    }
    return bin2hex(openssl_random_pseudo_bytes($length));
}


/**
 * Limit String By Words
 *
 * @param  string  $string The string
 * @param  integer $limit  number of words to limit
 * @param  string  $more   string to append after word limit has reached
 * @return string
 */
function limit_words($string, $limit = 40, $more = '...')
{
    $string = strip_tags($string);
    $string = explode(' ', $string, $limit);
    if (count($string) >= $limit) {
        array_pop($string);
        $string = implode(" ", $string) . $more;
    } else {
        $string = implode(" ", $string);
    }

    $string = str_replace("\n", '', $string);
    return $string;
}

function limit_string($string, $limit = 60, $more = '...')
{
    $limit = (int) $limit;

    if (mb_strlen($string) < $limit) {
        return $string;
    }

    return mb_substr($string, 0, $limit) . $more;
}

/**
 * Format string for javascript output
 *
 * @param  string $string
 * @return string
 */
function js_string($string)
{
    // remove new lines
    $string = str_replace(["\n", "\r"], ' ', $string);
    $string = addslashes($string);
    return $string;
}

/**
 * Apply htmlspecialshars with ENT_QUOTES and UTF-8 as default
 *
 * @param  string  $input        The input
 * @param  boolean $doubleEncode Toggle double encode
 * @return string
 */
function html_escape($input, $doubleEncode = true)
{
    if (is_array($input)) {
        foreach (array_keys($input) as $key) {
            $input[$key] = html_escape($input[$key], $doubleEncode);
        }
        return $input;
    }

    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8', $doubleEncode);
}

function e($input, $doubleEncode = true)
{
    return html_escape($input, $doubleEncode);
}

function e_attr($input)
{
    return html_escape($input, true);
}

/**
 * Apply htmlentities with ENT_QUOTES, ENT_HTML5 and UTF-8 as default
 *
 * @param  string  $input        The input
 * @param  boolean $doubleEncode Toggle double encode
 * @return string
 */
function html_entities($input, $doubleEncode = true)
{
    if (is_array($input)) {
        foreach (array_keys($input) as $key) {
            $input[$key] = html_entities($input[$key], $doubleEncode);
        }
        return $input;
    }

    return htmlentities($input, ENT_QUOTES | ENT_HTML5, 'UTF-8', $doubleEencode);
}

/**
 * Unserialize value only if it was serialized.
 *
 *
 * @param string $original Maybe unserialized original, if is needed.
 * @return mixed Unserialized data can be any type.
 */
function maybe_unserialize($original)
{
    // don't attempt to unserialize data that wasn't serialized going in
    if (is_serialized($original)) {
        return @unserialize($original);
    }
    return $original;
}

/**
 * Check value to find if it was serialized.
 *
 * If $data is not an string, then returned value will always be false.
 * Serialized data is always a string.
 *
 *
 * @param string $data   Value to check to see if was serialized.
 * @param bool   $strict Optional. Whether to be strict about the end of the string. Default true.
 * @return bool False if not serialized and true if it was.
 */
function is_serialized($data, $strict = true)
{
    // if it isn't a string, it isn't serialized.
    if (!is_string($data)) {
        return false;
    }
    $data = trim($data);
    if ('N;' == $data) {
        return true;
    }
    if (strlen($data) < 4) {
        return false;
    }
    if (':' !== $data[1]) {
        return false;
    }
    if ($strict) {
        $lastc = substr($data, -1);
        if (';' !== $lastc && '}' !== $lastc) {
            return false;
        }
    } else {
        $semicolon = strpos($data, ';');
        $brace     = strpos($data, '}');
        // Either ; or } must exist.
        if (false === $semicolon && false === $brace) {
            return false;
        }
        // But neither must be in the first X characters.
        if (false !== $semicolon && $semicolon < 3) {
            return false;
        }
        if (false !== $brace && $brace < 4) {
            return false;
        }
    }
    $token = $data[0];
    switch ($token) {
        case 's':
            if ($strict) {
                if ('"' !== substr($data, -2, 1)) {
                    return false;
                }
            } elseif (false === strpos($data, '"')) {
                return false;
            }
            // or else fall through
        case 'a':
        case 'O':
            return (bool) preg_match("/^{$token}:[0-9]+:/s", $data);
        case 'b':
        case 'i':
        case 'd':
            $end = $strict ? '$' : '';
            return (bool) preg_match("/^{$token}:[0-9.E-]+;$end/", $data);
    }
    return false;
}

/**
 * Check whether serialized data is of string type.
 *
 *
 * @param string $data Serialized data.
 * @return bool False if not a serialized string, true if it is.
 */
function is_serialized_string($data)
{
    // if it isn't a string, it isn't a serialized string.
    if (!is_string($data)) {
        return false;
    }
    $data = trim($data);
    if (strlen($data) < 4) {
        return false;
    } elseif (':' !== $data[1]) {
        return false;
    } elseif (';' !== substr($data, -1)) {
        return false;
    } elseif ($data[0] !== 's') {
        return false;
    } elseif ('"' !== substr($data, -2, 1)) {
        return false;
    } else {
        return true;
    }
}

/**
 * Serialize data, if needed.
 *
 *
 * @param string|array|object $data Data that might be serialized.
 * @return mixed A scalar data
 */
function maybe_serialize($data)
{
    if (is_array($data) || is_object($data)) {
        return serialize($data);
    }

    // Double serialization is required for backward compatibility.
    // See https://core.trac.wordpress.org/ticket/12930
    // Also the world will end. See WP 3.6.1.
    if (is_serialized($data, false)) {
        return serialize($data);
    }

    return $data;
}

/**
 * Remove specific value from one-dimentional array
 *
 * @return array
 */
function array_remove_value()
{
    $args = func_get_args();
    return array_diff($args[0], array_slice($args, 1));
}

/**
 * Returns if two arrays' values are exactly the same (regardless of keys and order),
 *
 * @param  array  $arrayA
 * @param  array  $arrayB
 * @return boolean
 */
function array_identical_values(array $arrayA, array $arrayB)
{
     sort($arrayA);
     sort($arrayB);

     return $arrayA == $arrayB;
}

/**
 * Insert a value or key/value pair after a specific key in an array.  If key doesn't exist, value is appended
 * to the end of the array.
 *
 * @param array $array
 * @param string $key
 * @param array $new
 *
 * @return array
 */
function array_insert_after(array &$array, $key, array $new)
{
    $keys  = array_keys($array);
    $index = array_search($key, $keys);
    $pos   = false === $index ? count($array) : $index + 1;
    $array = array_merge(array_slice($array, 0, $pos), $new, array_slice($array, $pos));
    return true;
}

function get_file_data($file, array $all_headers)
{
    $fp = fopen($file, 'r');

    $file_data = fread($fp, 8192);

    fclose($fp);

    $file_data = str_replace("\r", "\n", $file_data);

    foreach ($all_headers as $field => $regex) {
        if (preg_match('/^[ \t\/*#@]*' . preg_quote($regex, '/') . ':(.*)$/mi', $file_data, $match) && $match[1]) {
            $all_headers[$field] = _cleanup_header_comment($match[1]);
        } else {
            $all_headers[$field] = '';
        }
    }

    return $all_headers;
}

/**
 * Strip close comment and close php tags from file headers
 *
 * @param  string $str
 * @return string
 */
function _cleanup_header_comment($str)
{
    return trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', $str));
}

/*----------------------------------------
*   URL RELATED FUNCTIONS
*
* ----------------------------------------
*/

/**
 * UTF-8 aware parse_url() replacement.
 *
 * @link http://php.net/manual/en/function.parse-url.php#114817
 *
 * @return array
 */
function mb_parse_url($url)
{
    $enc_url = preg_replace_callback('%[^:/@?&=#]+%usD', function ($matches) {
        return urlencode($matches[0]);
    }, $url);

    $parts = parse_url($enc_url);

    if (!$parts) {
        return false;
    }

    foreach ($parts as $name => $value) {
        $parts[$name] = urldecode($value);
    }

    return $parts;
}

/**
 * Detect the base URL
 *
 * @return string
 */
function detect_base_uri()
{
    $protocol = is_https() ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME'];
    $scriptUri = str_replace(basename($script), '', $script);
    return $protocol . $host . $scriptUri;
}

/**
 * Detect cookie domain
 *
 * @param  string $url Manual URL Input (optional)
 * @return string
 */
function detect_cookie_domain($url = null)
{
    if (!is_string($url)) {
        $url = detect_base_uri();
    }

    $baseHost = parse_url($url, PHP_URL_HOST);

    // An IP address, k! lol :|
    if (filter_var($baseHost, FILTER_VALIDATE_IP)) {
        return '';
    }

    // No dot in domain? Bet we are on localhost!
    if (mb_strpos($baseHost, '.') === false) {
        return '';
    }
    return '.' . $baseHost;
}

/**
 * Returns if current connection is https or not
 *
 * @return boolean [description]
 */
function is_https()
{
    return !empty($_SERVER['HTTPS']) && (string)$_SERVER['HTTPS'] !== 'off';
}

/**
 * Send no cache headers
 *
 * @return
 */
function no_cache_headers()
{
    header_remove('Last-Modified');
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
}

/**
 * Force redirect using native PHP, no Slim dependencies
 *
 * @param  string  $path   Path to redirect
 * @param  boolean $native Toggle redirect within site or full URL
 * @param  boolean $cache  Toggle sending no cache headers defaults to FALSE
 * @return
 */
function forceredirect_to($path, $native = true, $cache = false)
{
    if ($native) {
        $path = trailingslashit(detect_base_uri()) . untrailingslashit($path);
    }
    if (!$cache) {
        no_cache_headers();
    }
    header("Location: {$path}");
    exit(1);
}


/**
 * Apply batch functions to a var
 *
 * @param  string $var       the input
 * @param  string $functions Pipe separated list of functions. eg. strip_tags|trim
 * @return string
 */
function batch_filter($var, $functions)
{
    foreach (explode('|', $functions) as $func) {
        if (is_callable($func)) {
            $var = $func($var);
        } else {
            throw new \RuntimeException("Unable to call function {$func}");
        }
    }
    return $var;
}

/**
 * Get actual top level domain name (not subdomain) from URL
 *
 * @param  string $url The URL
 * @return string|boolean
 */
function get_domain_name($url)
{
    $host = parse_url($url, PHP_URL_HOST);

    // For IPs
    if (filter_var($host, FILTER_VALIDATE_IP)) {
        return $host;
    }

    // Fix for localhost
    if (strpos($host, '.') === false) {
        return $host;
    }

    $host_names = explode(".", $host);
    $bottom_host_name = $host_names[count($host_names)-2] . "." . $host_names[count($host_names)-1];

    return $bottom_host_name;
}


/*----------------------------------------
*   FILESYSTEM RELATED FUNCTIONS
*
* ----------------------------------------
*/


/**
 * Recursively copy files from one directory to another
 *
 * @param String $src - Source of files being moved
 * @param String $dest - Destination of files being moved
 * @see https://ben.lobaugh.net/blog/864/php-5-recursively-move-or-copy-files
 */
function rcopy($src, $dest)
{
    // If source is not a directory stop processing
    if (!is_dir($src)) {
        return false;
    }

    // If the destination directory does not exist create it
    if (!is_dir($dest)) {
        if (!mkdir($dest)) {
            // If the destination directory could not be created stop processing
            return false;
        }
    }

    // Open the source directory to read in files
    $i = new DirectoryIterator($src);
    foreach ($i as $f) {
        if ($f->isFile()) {
            copy($f->getRealPath(), "$dest/" . $f->getFilename());
        } elseif (!$f->isDot() && $f->isDir()) {
            rcopy($f->getRealPath(), "$dest/$f");
        }
    }
}

/**
 * Recursively move files from one directory to another
 *
 * @param String $src - Source of files being moved
 * @param String $dest - Destination of files being moved
 * @see https://ben.lobaugh.net/blog/864/php-5-recursively-move-or-copy-files
 */
function rmove($src, $dest)
{
    // If source is not a directory stop processing
    if (!is_dir($src)) {
        return false;
    }

    // If the destination directory does not exist create it
    if (!is_dir($dest)) {
        if (!mkdir($dest)) {
            // If the destination directory could not be created stop processing
            return false;
        }
    }

    // Open the source directory to read in files
    $i = new DirectoryIterator($src);
    foreach ($i as $f) {
        if ($f->isFile()) {
            rename($f->getRealPath(), "$dest/" . $f->getFilename());
        } elseif (!$f->isDot() && $f->isDir()) {
            rmove($f->getRealPath(), "$dest/$f");
            rrmdir($f->getRealPath());
        }
    }

    rrmdir($src);
}

/**
 * Recursively removes a folder along with all its files and directories
 *
 * @param string $path
 * @see https://ben.lobaugh.net/blog/910/php-recursively-remove-a-directory-and-all-files-and-folder-contained-within
 */
function rrmdir($path)
{
    // One may accidently provide a file
    if (is_file($path)) {
        return unlink($path);
    }

    // If source is not a directory stop processing
    if (!is_dir($path)) {
        return false;
    }

    // Open the source directory to read in files
    $i = new DirectoryIterator($path);
    foreach ($i as $f) {
        if ($f->isFile()) {
            unlink($f->getRealPath());
        } elseif (!$f->isDot() && $f->isDir()) {
            rrmdir($f->getRealPath());
        }
    }
    rmdir($path);
}

# #
# NOTE:
# The functions below depends on Slim
# So they shouldn't be called untill a single instance of Slim is created
# #

/**
 * Quick access the slim app instance anywhere
 *
 * @return Slim\Slim
 */
function app()
{
    return Slim::getInstance();
}

/**
 * Store values into flash
 *
 * @param  string $key
 * @param  string $value
 * @return
 */
function flash($key, $value)
{
    return app()->flash($key, $value);
}

/**
 * A wrapper to read app config, not to modify it
 *
 * @param  string $key
 * @param  string $fallback
 * @return mixed
 */
function config($key, $fallback = null)
{
    $value = app()->config($key);

    if (is_null($value)) {
        return $fallback;
    }

    return $value;
}


/**
 * Add global data to view
 *
 * @param  array  $data
 * @return
 */
function append_view_data(array $data)
{
    return app()->view->replace($data);
}

/**
 * Render template
 *
 * @param  string $template
 * @param  array  $data
 * @param  integer $status
 * @return
 */
function view($template, $data = [], $status = null)
{
    return app()->render($template, $data, $status);
}

function ajax_view($template, $data = [], $status = null)
{
    return app()->view->renderAjax($template, $data, $status);
}

/**
 * Fetch template
 *
 * @param  string $template
 * @param  array  $data
 * @return
 */
function view_fetch($template, $data = [])
{
    return app()->view->fetch($template, $data);
}

function response_status($status)
{
    return app()->response->setStatus($status);
}

function response_body($content)
{
    return app()->response->setBody($content);
}

function view_set($key, $value)
{
    return app()->view->set($key, $value);
}

function view_get($key, $fallback = null)
{
    return app()->view->get($key, $fallback);
}

function view_data(array $data)
{
    return app()->view->replace($data);
}

function get_cookie($name, $deleteIfInvalid = true)
{
    return app()->getCookie($name, $deleteIfInvalid);
}

function set_cookie($name, $value, $time = null, $path = null, $domain = null, $secure = null, $httponly = null)
{
    return app()->setCookie($name, $value, $time, $path, $domain, $secure, $httponly);
}

function delete_cookie($name, $path = null, $domain = null, $secure = null, $httponly = null)
{
    return app()->deleteCookie($name, $path, $domain, $secure, $httponly);
}

/**
 * Output JSON
 *
 * @param  mixed   $data
 * @param  integer $status
 * @param  array   $crossOrigin
 * @return
 */
function json($data, $status = null, array $crossOrigin = [])
{
    app()->response->header('Content-Type', 'application/json; charset=utf-8');
    if (!empty($crossOrigin)) {
        app()->response->header('Access-Control-Allow-Origin', join(', ', $crossOrigin));
    }

    if (!is_null($status)) {
        app()->response->status($status);
    }

    // Enable pretty print for developers
    if (is_dev()) {
        $data = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    } else {
        $data = json_encode($data, JSON_UNESCAPED_SLASHES);
    }


    return app()->response->setBody($data);
}

/**
 * Ajax JSON Output for Forms
 *
 * @param  array   $response
 * @param  integer $status
 * @param  array   $crossOrigin
 * @return string
 */
function ajax_json(array $response, $status = 200, array $crossOrigin = [])
{
    $defaults = [
        'message' => '',
        'redirect' => false,
        'dismissable' => true,
        'type' => 'danger'
    ];

    $response = array_merge($defaults, $response);

    return json($response, $status, $crossOrigin);
}

/**
 * Alias for ajax_json()
 *
 * @param  array   $response
 * @param  integer $status
 * @param  array   $crossOrigin
 * @return string
 */
function ajax_form_json(array $response, $status = 200, array $crossOrigin = [])
{
    return ajax_json($response, $status, $crossOrigin);
}

/**
 * Sends no cache headers using the slim response object
 * This is the recommended way to send the no cache headers after the app instance has been created
 *
 * @return
 */
function slim_no_cache_headers()
{
    $headers = [
        'Cache-Control' => "no-cache, must-revalidate, max-age=0\npost-check=0, pre-check=0",
        'Expires' => 'Wed, 11 Jan 1984 05:00:00 GMT',
        'Pragma' => 'no-cache',
        'Last-Modified' => null
    ];

    foreach ($headers as $key => $value) {
        app()->response->header($key, $value);
    }
}

function slim_remove_no_cache_headers()
{
    $headers = [
        'Cache-Control' => null,
        'Expires' => null,
        'Pragma' => null
    ];

    foreach ($headers as $key => $value) {
        app()->response->header($key, $value);
    }
}

/**
 * Generate URL for specific named route
 *
 * @param  string $routeName
 * @param  array  $params
 * @return string
 */
function url_for($routeName, array $params = [], $relative = false)
{
    return app()->urlFor($routeName, $params, $relative);
}

/**
 * Quick access the Site URI
 *
 * @param string $path (Optional) The path
 * @return string
 */
function base_uri($path = '')
{
    $app = Slim::getInstance();

    if ($app->config('uri')) {
        return trailingslashit($app->config('uri')) . unleadingslashit($path);
    }

    $req = Slim::getInstance()->request();
    $uri = $req->getUrl();
    $uri .= $req->getRootUri();
    $uri .= leadingslashit($path);
    return $uri;
}

/**
 * Quick access upload directory uri
 *
 * @param string $path (Optional) The path
 * @return string
 */
function uploads_uri($path = '')
{
    $uploadsPath = trailingslashit(UPLOADS_DIR) . unleadingslashit($path);
    return base_uri($uploadsPath);
}

/**
 * Quick access site directory uri
 *
 * @param string $path (Optional) The path
 * @return string
 */
function site_uri($path = '')
{
    $sitePath = trailingslashit(SITE_DIR) . unleadingslashit($path);
    return base_uri($sitePath);
}

/**
 * Quick access theme directory uri
 *
 * @param string $path (Optional) The path
 * @return string
 */
function theme_uri($path = '')
{
    $themePath = trailingslashit(THEME_DIR) . unleadingslashit($path);
    return base_uri($themePath);
}

/**
 * Redirect to URL
 *
 * @param  string  $url       url to redirect to
 * @param  integer $httpStatus Http status code
 * @param  boolean $cache      Toggle cache
 * @return string
 */
function redirect($url, $httpStatus = 302, $cache = false)
{
    if (!$cache) {
        slim_no_cache_headers();
    } else {
        slim_remove_no_cache_headers();
    }

    return app()->redirect($url, $httpStatus);
}

/**
 * Redirect to a named route
 *
 * @param  string    $routeName      The route name
 * @param  array     $params         Associative array of URL parameters and replacement values
 * @param  boolean   $cache          Toggle sending no cache headers defaults to FALSE
 * @return
 */
function redirect_to($routeName, array $params = [], $cache = false)
{
    $url = url_for($routeName, $params);
    return redirect($url, 302, $cache);
}

/**
 * Redirect to current route ( self reload but named route style )
 *
 * @param boolean $queryParams Whether to append the query params with the url, defaults to TRUE
 * @return
 */
function redirect_to_current_route($queryParams = true, array $paramsIgnore = [])
{
    return redirect(get_current_route_uri($queryParams, $paramsIgnore));
}

function get_current_route_uri($queryParams = false, array $paramsIgnore = [])
{
    $app = app();
    if (empty(get_current_route_name())) {
        $url = base_uri($app->request->getResourceUri());
    } else {
        $url = url_for(get_current_route_name(), get_current_route_params());
    }


    if ($queryParams) {
        $query = request_build_query($paramsIgnore, null);

        if (!empty($query)) {
            $url .= "?" . request_build_query($paramsIgnore, null);
        }
    }

    return $url;
}

/**
 * Enable GZIP compression
 *
 * @return
 */
function enable_gzip_compression()
{
    $app = Slim::getInstance();
    $accept = $app
    ->request
    ->headers
    ->get('Accept-Encoding');

    if (!substr_count($accept, 'gzip')) {
        return false;
    }

    // Start the magic!
    ob_start('ob_gzhandler');
}

/**
 * Returns if the request came within from the same site or not
 *
 * @return boolean
 */
function is_trusted_referer()
{
    $app = Slim::getInstance();
    $referer = $app
    ->request
    ->getReferer();

    if (empty($referer)) {
        return false;
    }

    return is_trusted_domain($referer);
}

/**
 * Returns if provided domain is a trusted domain or not
 *
 * @param  string  $url URL or domain name
 * @return boolean
 */
function is_trusted_domain($url)
{
    $app = Slim::getInstance();
    $domain = get_domain_name($url);
    return in_array($domain, $app->config('trusted_domains'));
}

/**
 * Returns ?redirect_to= value ensuring not spam
 *
 * @param  string  $fallback
 * @param  boolean $redirect
 * @param  integer $httpStatus
 * @param  boolean $cache
 * @return string|void
 */
function get_redirect_to_uri($fallback = null, $redirect = false, $httpStatus = 302, $cache = false)
{
    $app = Slim::getInstance();

    if (!is_string($fallback)) {
        $fallback = get_referer_uri();
    }

    $uri = $fallback;
    $redirectUri = urldecode($app
        ->request
        ->get('redirect_to'));


    $sessionUri = urldecode($app->session->get('redirect_to', null));

    if (!empty($redirectUri)) {
        $uri = $redirectUri;
    } elseif (!empty($sessionUri)) {
        $uri = $sessionUri;
    } else {
        $uri = $fallback;
    }


    if (filter_var($redirectUri, FILTER_VALIDATE_URL)) {
        if (is_trusted_domain($redirectUri)) {
            $uri = $redirectUri;
        }
    }

    if (!$redirect) {
        return $uri;
    }

    return redirect($uri, $httpStatus, $cache);
}

/**
 * Redirects to ?redirect_to= value ensuring not spam
 *
 * @param  string  $fallback
 * @param  integer $httpStatus
 * @param  boolean $cache
 *
 * @return
 */
function follow_redirect_to_uri($fallback = null, $httpStatus = 302, $cache = false)
{
    return get_redirect_to_uri($fallback, true, $httpStatus, $cache);
}

function get_referer_uri($fallback = null)
{
    $uri = app()->request->getReferer();

    if (!is_trusted_referer()) {
        $uri = $fallback;
    }

    return $uri;
}

/**
 * Redirect to the http referer if it's within
 *
 * @param  string  $fallback
 * @param  integer $httpStatus
 * @param  boolean $cache
 * @return
 */
function follow_referer_uri($fallback = null, $httpStatus = 302, $cache = false)
{
    return redirect(get_referer_uri($fallback), $httpStatus, $cache);
}

/**
 * Multibyte RegExp based URL validity test
 *
 * @param  string  $subject Input string
 * @return boolean
 */
function is_url($subject)
{
    return (bool) preg_match('%^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%ui', $subject);
}

/**
 * Build a query string from REQUEST
 *
 * @param  array  $ignore keys to ignore
 * @param  string $type   request type, GET or POST
 * @return string
 */
function request_build_query(array $ignore = [], $amp = -1)
{
    $app = Slim::getInstance();
    $data = $app
    ->request
    ->get();

    foreach ($ignore as $key) {
        if (isset($data[$key])) {
            unset($data[$key]);
        }
    }

    $query = http_build_query($data);

    if (empty($query)) {
        return $query;
    }

    if ($amp === null || $amp === false) {
        return $query;
    } elseif ($amp === -1) {
        return '&' . $query;
    } else {
        return $query . '&';
    }
}

/**
 * Get current route name, only accssible after route has been dispatched
 *
 * @return string
 */
function get_current_route_name()
{
    $app = Slim::getInstance();
    if ($app
        ->router
        ->getCurrentRoute()) {
        return $app
        ->router
        ->getCurrentRoute()
        ->getName();
    }

    return null;
}


/**
 * Check if provided name matches with current route name
 *
 * @param  string  $name
 * @return boolean
 */
function is_route($name)
{
    return (string) $name === get_current_route_name();
}

/**
 * Get current routes params, only accssible after route has been dispatched
 *
 * @return array
 */
function get_current_route_params()
{
    $app = Slim::getInstance();
    if ($app
        ->router
        ->getCurrentRoute()) {
        return $app
        ->router
        ->getCurrentRoute()
        ->getParams();
    }

    return [];
}

/**
 * Halt if request method isn't provided type
 *
 * @param  string|array $methods
 * @param  string $message
 * @return
 */
function __halt_if_request_method_isnt($methods, $message = 'Nice try ;)')
{
    $app = Slim::getInstance();
    $methods = (array) $methods;
    $methods = array_map('mb_strtolower', $methods);
    $test = mb_strtolower($app->request->getMethod());
    if (!in_array($test, $methods)) {
        $app->halt(500, $message);
    }
}

/**
 * Returns if current request is ajax
 *
 * @return boolean
 */
function is_ajax()
{
    return app()->request->isAjax();
}


function sp_password_hash($input)
{
    return password_hash($input, PASSWORD_BCRYPT);
}

/**
 * Output server error
 *
 * @param  string  $title
 * @param  string  $message
 * @param  integer $status
 * @return string
 */
function fatal_server_error($title, $message, $status = 403)
{
    $message = nl2br($message, true);
    $body = <<<EOD
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>{$title}</title>
</head><body>
<h1>{$title}</h1>
<p>{$message}
</p>
<hr>
<address>{$_SERVER['SERVER_SIGNATURE']}</address>
</body></html>
EOD;
    no_cache_headers();
    http_response_code($status);
    echo $body;
    exit;
}
