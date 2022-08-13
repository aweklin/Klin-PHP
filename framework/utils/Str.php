<?php

namespace Framework\Utils;

/**
 * Contains different methods used for string manipulation.
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
final class Str {

    /**
     * Checks if the search string is the first occurrence in the subject.
     * 
     * @param string $string The text being searched.
     * @param string $search The actual text being searched.
     * @param bool $ignoreCase Case sensitivity is ignored by default. When false or null is specified, the result matches the case for comparison.
     * 
     * @return bool
     */
    public static function startsWith(string $string, string $search, bool $ignoreCase = true) : bool {
        if (self::isEmpty($string)) return false;
        if (\mb_strlen($search) == 0 && $search !== \mb_substr($string, 0, 1)) return false;
        return substr_compare($string, $search, 0, mb_strlen($search), $ignoreCase) === 0;
    }

    /**
     * Checks if the search string is the last occurrence in the subject.
     * 
     * @param string $string The text being searched.
     * @param string $search The actual text being searched.
     * @param bool $ignoreCase Case sensitivity is ignored by default. When false or null is specified, the result matches the case for comparison.
     * 
     * @return bool
     */
    public static function endsWith(string $string, string $search, bool $ignoreCase = true) : bool {
        if (self::isEmpty($string)) return false;
        if (\mb_strlen($search) == 0 && $search !== \mb_substr($string, \mb_strlen($string) - 1, 1)) return false;
        return substr_compare($string, $search, -mb_strlen($search), null, $ignoreCase) === 0;
    }

    
    /**
     * Checks if the value passed is null or empty and returns a value indicating the status of the check.
     * 
     * @param mixed $value The value being checked.
     */
    public static function isEmpty(string $value = '') : bool {
        if (!$value) return true;
        if (mb_strlen(trim($value)) == 0) return true;
        return false;
    }

    /**
     * Checks if the given string contains the search phrase.
     * 
     * @param string $string The full string to search through.
     * @param string $search The text being searched.
     * 
     * @return bool
     */
    public static function contains(string $string, string $search) : bool {
        if (self::isEmpty($string)) return false;
        if ($string) $string = trim($string);
        return mb_strpos($string, $search) !== false;
    }

    /**
     * Converts the given text into lowercase.
     * 
     * @param string $string Text to convert.
     * 
     * @return string
     */
    public static function toLower(string $string) : string {
        return ($string ? mb_strtolower($string) : '');
    }

    /**
     * Converts the given text into uppercase.
     * 
     * @param string $string Text to convert.
     * 
     * @return string
     */
    public static function toUpper(string $string) : string {
        return ($string ? mb_strtoupper($string) : '');
    }

    /**
     * Generates a random (alphanumeric) string based on the given length.
     * 
     * @param int $length Indicates the length of string to generate
     * 
     * @return string
     */
	public static function generateRandomString(int $length = 8) : string {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$string = mb_substr(str_shuffle($chars), 0, $length);
		
		return $string;
	}
	
    /**
     * Generates a random (alphabet only) string based on the given length.
     * 
     * @param int $length Indicates the length of string to generate
     * 
     * @return string
     */
	public static function generateOnlyRandomString(int $length = 8) : string {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$string = mb_substr(str_shuffle($chars), 0, $length);
		
		return $string;
	}
	
    /**
     * Generates a random number only based on the given length.
     * 
     * @param int $length Indicates the length of string to generate
     * 
     * @return string
     */
	public static function generateRandomNumbers(int $length = 8) : string {
		$chars = "0123456789";
		$string = mb_substr(str_shuffle($chars), 0, $length);
		
		return $string;
	}
    
    /**
     * Confirms if the value supplied is a valid email address format.
     * 
     * @param string $email The value being validated.
     * 
     * @return bool
     */
	public static function isValidEmail($email) : bool {
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}

	/**
	* Generate a random UUID version 4
	*
	* Warning: This method should not be used as a random seed for any cryptographic operations.
	* Instead you should use the openssl or mcrypt extensions.
	*
	* @see http://www.ietf.org/rfc/rfc4122.txt
	* @return string RFC 4122 UUID
	* @copyright Matt Farina MIT License https://github.com/lootils/uuid/blob/master/LICENSE
	*/
	public static function uuid() {
		return sprintf(
		    '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		    // 32 bits for "time_low"
		    mt_rand(0, 65535),
		    mt_rand(0, 65535),
		    // 16 bits for "time_mid"
		    mt_rand(0, 65535),
		    // 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
		    mt_rand(0, 4095) | 0x4000,
		    // 16 bits, 8 bits for "clk_seq_hi_res",
		    // 8 bits for "clk_seq_low",
		    // two most significant bits holds zero and one for variant DCE1.1
		    mt_rand(0, 0x3fff) | 0x8000,
		    // 48 bits for "node"
		    mt_rand(0, 65535),
		    mt_rand(0, 65535),
		    mt_rand(0, 65535)
		);
	}

	/**
	 * Removes all whitespace in the given string.
	 * 
	 * @param	string	$value	The string to remove all the whitespaces from.
	 * 
	 * @return	string
	*/
	static function removeSpaces($value) : string {
		return preg_replace('/\s+/', '', $value);
	}

    static function encodeToBase64(string $string) : string {
        if (self::isEmpty($string)) return '';

        return base64_encode($string);
    }

    static function decodeFromBase64(string $encoded, bool $strict = false) : string {
        if (self::isEmpty($encoded)) return '';

        return base64_decode($encoded, $strict);
    }
	
    static function retainOnlyAlphaNumeric(string $value) {
        if (self::isEmpty($value)) return '';

        $result = preg_replace('/[\@\.\;\" "]+/', '', $value);
        return $result;
    }
	
    static function retainOnlyAlphabets($value) {
        if (self::isEmpty($value)) return '';
        
        $result = preg_replace('/[0-9\@\.\;\" "]+/', '', $value);
        return $result;
    }
	
}