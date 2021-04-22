<?php

namespace Framework\Core\Validators\Rules;

use Framework\Core\Validators\Rules\ValidationRuleBase;
use Framework\Utils\Str;

/**
 * Checks to see if the value passed is up to the minimum length specified.
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
class MinimumLengthRule extends ValidationRuleBase {

    /**
     * Stores the length of character(s) needed to pass validation.
     */
    private $_length = 0;

    /**
     * Initializes a new instance of the MinimumLengthRule validation class with the error message to be returned to the user.
     * 
     * @param int $length Indicates the minimum length of character(s) required
     * @param string $errorMessage The actual error message the user sees if validation fails.
     */
    public function __construct(int $length, string $errorMessage = 'must be at least %u character%s long') {
        $this->_length = $length;
        $this->errorMessage = sprintf($errorMessage, $length, ($length > 0 ? 's' : ''));
    }

    /**
     * Checks to see if the validation succeed or not and returns a boolean value, indicating the status of the validation.
     * 
     * @param mixed $value The actual value to be validated.
     * 
     * @return bool
     */
    function isValid($value = null) : bool {
        if (Str::isEmpty($value)) return false;
        return mb_strlen($value) >= $this->_length;
    }
}