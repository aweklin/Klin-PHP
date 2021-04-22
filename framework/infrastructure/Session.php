<?php

namespace Framework\Infrastructure;

final class Session {

    public static function exists($name) {
        return isset($_SESSION[$name]);
    }

    public static function get($name) {
        if (!self::exists($name)) return null;
        return $_SESSION[$name];
    }

    public static function set($name, $value) {
        $_SESSION[$name] = $value;
    }

    public static function delete($name) {
        if (self::exists($name)) {//dbd('Exists to be deleted');
            unset($_SESSION[$name]);
        }
    }

    public static function getUserAgent() {
        return preg_replace('/\/[a-zA-Z0-9.]+/', '', $_SERVER['HTTP_USER_AGENT']);
    }

}