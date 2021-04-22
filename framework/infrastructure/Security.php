<?php

namespace Framework\Infrastructure;

use Framework\Infrastructure\Session;

final class Security {

    public static function encrypt(string $value) : string {
        if (!$value) return '';
        return password_hash($value, PASSWORD_DEFAULT);
    }

    public static function isVerified(string $planText, string $encryptedText) : bool {
        return password_verify($planText, $encryptedText);
    }

    public static function getCurrentUserId() {
        if (!Session::exists(SECURITY_CURRENT_LOGGED_IN_USER_ID)) return null;
        return Session::get(SECURITY_CURRENT_LOGGED_IN_USER_ID);
    }

}