<?php

namespace Framework\Infrastructure;

final class Cookie {

    public static function exists($name) {
        return isset($_COOKIE[$name]);
    }

    public static function set($name, $value, $expiry) {
        setCookie($name, $value, time() + $expiry, '/');
    }

    public static function get($name) {
        if (!self::exists($name)) return null;

        return $_COOKIE[$name];
    }

    public static function delete($name) {
        if (self::exists($name)) {
            self::set($name, '', time() - 1);
        }
    }

}