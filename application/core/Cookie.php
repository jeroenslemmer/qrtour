<?php

/**
 * Session class
 *
 * handles the session stuff. creates session when no one exists, sets and gets values, and closes the session
 * properly (=logout). Not to forget the check if the user is logged in or not.
 */
class Cookie
{
    public static $standardExpirySeconds = 86400; // one day!

    /**
     * tests if cookies are enabled
     *
     */ 

    public static function enabled()
    {
        setcookie("test_cookie", "test", time() + 60, '/');
        return (count($_COOKIE) > 0);
    }

    /**
     * sets a specific value to a specific key of the session
     *
     * @param mixed $key key
     * @param mixed $value value
     */
    public static function set($key, $value, $expirySeconds = 0)
    {
        $expirySeconds = ($expirySeconds == 0)?self::$standardExpirySeconds:$expirySeconds;
        setcookie($key, $value, time() + $expirySeconds, '/');
    }

    /**
     * gets/returns the value of a specific key of the cookie
     *
     * @param mixed $key Usually a string, right ?
     * @return mixed the key's value or nothing
     */
    public static function get($key)
    {
        if (isset($_COOKIE[$key])) {
            $value = $_COOKIE[$key];

            // filter the value for XSS vulnerabilities
            return Filter::XSSFilter($value);
        }
        return '';
    }

    /**
     * deletes the cookie 
     */
    public static function delete($key)
    {
        self::set($key,'',time()-3600);
    }
}
