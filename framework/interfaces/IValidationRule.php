<?php

namespace Framework\Interfaces;

/**
 * Defines a method indicating the success or failure of the validation, and the error message returned if validation fails.
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
interface IValidationRule {
    /**
     * Checks to see if the validation succeed or not and returns a boolean value, indicating the status of the validation.
     * 
     * @param mixed $value The actual value to be validated.
     * 
     * @return bool
     */
    function isValid($value = null) : bool;
    /**
     * Retrieves the error message from the validation. If validation was not done, an empty string is returned.
     * 
     * @return string
     */
    function getErrorMessage() : string;
}