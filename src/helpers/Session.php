<?php

namespace spark\helpers;

/**
 * Session Helper class
 *
 * This is a general-purpose class that allows to manage PHP built-in sessions
 * and the session variables passed via $_SESSION superglobal.
 *
 * @package spark
 */
class Session
{
    /**
     * Get a session variable.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return DotArray::get($_SESSION, $key, $default);
    }

    /**
     * Set a session variable.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        DotArray::set($_SESSION, $key, $value);
        return $this;
    }

    /**
     * Delete a session variable.
     *
     * @param string $key
     */
    public function delete($key)
    {
        DotArray::delete($_SESSION, $key);
        return $this;
    }

    /**
     * Clear all session variables.
     */
    public function clear()
    {
        $_SESSION = [];
    }

    public function all()
    {
        return $_SESSION;
    }

    /**
     * Check if a session variable is set.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function exists($key)
    {
        return DotArray::exists($_SESSION, $key);
    }

    /**
     * Get or regenerate current session ID.
     *
     * @param bool $new
     *
     * @return string
     */
    public static function id($new = false)
    {
        if ($new && session_id()) {
            session_regenerate_id(true);
        }

        return session_id() ? : '';
    }

    /**
     * Destroy the session.
     */
    public static function destroy()
    {
        if (self::id()) {
            session_unset();
            session_destroy();

            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 4200,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }
        }
    }
}
