<?php

namespace Framework\Core\Validators\Rules;

use Framework\Core\Validators\Rules\ValidationRuleBase;
use Framework\Utils\Str;

/**
 * Checks to see if the value passed is up to the maximum length specified.
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
class ExactLengthRule extends ValidationRuleBase {

    /**
     * Stores the length of character(s) needed to pass validation.
     */
    private $_length = 0;

    /**
     * Initializes a new instance of the ExactLengthRule validation class with the error message to be returned to the user.
     * 
     * @param int $length Indicates the exact length of character(s) expected.
     * @param string $errorMessage The actual error message the user sees if validation fails.
     */
    public function __construct(int $length, string $errorMessage = 'must be %u %s long') {
        $this->_length = $length;
        //$this->errorMessage = sprintf($errorMessage, $length, ($length > 0 ? 's' : ''));
        $this->errorMessage = $errorMessage;
    }

    /**
     * Checks to see if the validation succeed or not and returns a boolean value, indicating the status of the validation.
     * 
     * @param mixed $value The actual value to be validated.
     * 
     * @return bool
     */
    function isValid($value = null) : bool {
        $this->errorMessage = sprintf($this->errorMessage, $this->_length, ($this->isNumberValidation ? 'digit' : 'character') . ($this->_length > 0 ? 's' : ''));
        if (Str::isEmpty($value)) return false;
        return mb_strlen($value) == $this->_length;
    }

}