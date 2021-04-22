<?php

declare(strict_types=1);

namespace Framework\Utils;

/**
 * Contains methods to manipulate array.
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
final class Ary {

    /**
     * Checks if the passed in array value is an associative array.
     * 
     * @param array $arr The array being checked.
     * 
     * @return bool
     */
    public static function isAssociative(array $arr): bool {
        // credit: https://stackoverflow.com/questions/173400/how-to-check-if-php-array-is-associative-or-sequential
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * Converts a given object to its array equivalent.
     * 
     * @param object $object The object to be converted. Null can be passed, but this means it will return an empty array.
     * 
     * @return array
     */
    public static function convertFromObject($object = null) : array {
        if (!$object) return [];

        return \json_decode(\json_encode($object), true);
    }

}