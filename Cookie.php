<?php
namespace Clases\Login;

class Cookie
{   
    const Session = 0;

    const ADay = 86400;

    const AWeek = 604800;

    const AMonth = 2592000;

    // technically this is only 30 days.
    const AYear = 31536000;

    static public function Day($amount = 1)
    {
        if (! is_numeric($amount) || ! is_int($amount) || $amount > 200) $amount = 1;

        return (self::ADay * $amount);
    }

    static public function Week($amount = 1)
    {
        if (! is_numeric($amount) || ! is_int($amount) || $amount > 200) $amount = 1;

        return (self::AWeek * $amount);
    }

    static public function Month($amount = 1)
    {
        if (! is_numeric($amount) || ! is_int($amount) || $amount > 200) $amount = 1;

        return (self::AMonth * $amount);
    }

    static public function Year($amount = 1)
    {
        if (! is_numeric($amount) || ! is_int($amount) || $amount > 200) $amount = 1;

        return (self::AYear * $amount);
    }

    static public function Exists($cookie)
    {
        return (isset($_COOKIE[$cookie]));
    }

    static public function IsEmpty($cookie)
    {
        return (empty($_COOKIE[$cookie]));
    }

    static public function Get($cookie, $def_value = '')
    {
        return ((self::Exists($cookie)) ? $_COOKIE[$cookie] : $def_value);
    }

    static public function Set($cookie, $value, $options = array())
    {
        $default_options = array('expiry' => self::AWeek,'path' => '/','domain' => (bool) true,'secure' => (bool) false,'httponly' => (bool) false);
        $cookie_set = false;

        if (! headers_sent())
        {
            foreach ($default_options as $option_key => $option_value)
            {
                if (! array_key_exists($option_key, $options)) $options[$option_key] = $default_options[$option_value];
            }

            $options['domain'] = (($options['domain'] === true) ? ('.' . $_SERVER['HTTP_HOST']) : '');
            
            $options['expiry'] = (int)((is_numeric($options['expiry']) ? ($options['expiry']+=time()) : strtotime($options['expiry'])));

            $cookie_set = @setcookie($cookie, $value, $options['expiry'], $options['path'], $options['domain'], $options['secure'], $options['httponly']);
            if ($cookie_set) $_COOKIE[$cookie] = $value;
        }

        return $cookie_set;
    }

    static public function Remove($cookie, $options = array())
    {
        $default_options = array('path' => '/','domain' => (bool) true,'secure' => (bool) false,'httponly' => (bool) false,'globalremove' => (bool) true);
        $return = false;

        if (! headers_sent())
        {
            foreach ($default_options as $option_key => $option_value)
            {
                if (! array_key_exists($option_key, $options)) $options[$option_key] = $default_options[$option_value];
            }

            $options['domain'] = (($options['domain'] === true) ? ('.' . $_SERVER['HTTP_HOST']) : '');

            if ($options['globalremove']) unset($_COOKIE[$cookie]);

            $return = @setcookie($cookie, '', (time() - 3600), $options['path'], $options['domain'], $options['secure'], $options['httponly']);
        }

        return $return;
    }
}
