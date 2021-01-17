<?php

namespace spark\drivers\Http;

use \Requests_Session;
use \Requests_Cookie_Jar;

/**
* Requests Http Wrapper
*
* @package spark
*/
class Http
{
    /**
     * Holds the Requests session to use accross the library
     *
     * @var object
     */
    protected static $session;

    /**
     * Prevent constructing this class
     */
    private function __construct()
    {
    }

    /**
     * Prevent cloning this class
     */
    private function __clone()
    {
    }

    /**
     * Prevent waking up this class
     */
    private function __wakeup()
    {
    }

    /**
     * Returns a static session of the Requests class
     *
     * @return object
     */
    public static function getSession()
    {
        if (!static::$session) {
            // Eww, I hate PSR-0
            static::$session = new Requests_Session;
            static::$session->useragent =
            'Mozilla/5.0 (Windows NT 6.3; WOW64) ' .
            'AppleWebKit/537.36 (KHTML, like Gecko) ' .
            'Chrome/60.0.2214.115 Safari/537.36';
            static::$session->headers['Accept'] =
            'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';
            static::$session->headers['Accept-Language'] = 'en-US,en;q=0.5';
            // Obvious -_-
            static::$session->headers['Referer'] = '';
            // Not works, still will try
            if (isset($_SERVER['REMOTE_ADDR'])) {
                static::$session->headers['X-Forwarded-For'] = $_SERVER['REMOTE_ADDR'];
            }
            static::$session->options['timeout'] = 100;
            static::$session->options['connect_timeout'] = 100;
            // we have snacks
            static::$session->options['cookies'] = new Requests_Cookie_Jar;
        }

        return self::$session;
    }
}
