<?php

namespace Framework\Decorator;

use Framework\Utils\Str;

/**
 * Allows various password operations to be performed.
*/
final class PasswordManager {
    
    /**
     * Generates a random but strong password based on the given length.
     * 
     * @param int $length Indicates the length of string to generate
     * 
     * @return string
     */
	public static function generateStrongPassword(int $length = 8) : string {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*_";
		$password = mb_substr(str_shuffle($chars), 0, $length);
		
		return $password;
	}
	
    /**
     * Confirms if the value supplied is a strong password.
     * A strong password must have at least one upper case, lower case, number and special character with the minimum length specified.
     * 
     * @param string $password The given password to test its strength.
     * @param string $minimumLength Specifies the minimum password length. The default is 6.
     * 
     * @return bool
     */
    public static function isStrongPassword($password, int $minimumLength = 6) : bool {
        if (Str::isEmpty($password)) return false;

        $isUpperCaseTestPassed  = preg_match('@[A-Z]@', $password);
        $isLowerCaseTestPassed  = preg_match('@[a-z]@', $password);
        $isNumberTestPassed     = preg_match('@[0-9]@', $password);
        $isSpecialCharacterPassed=preg_match('@[^\w]@', $password);

        return 
            (
                    !$isUpperCaseTestPassed 
                    || !$isLowerCaseTestPassed 
                    || !$isNumberTestPassed 
                    || !$isSpecialCharacterPassed 
                    || mb_strlen($password) < $minimumLength 
                ? false : true
            );
    }

}