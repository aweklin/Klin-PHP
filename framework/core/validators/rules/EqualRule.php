<?php

namespace Framework\Core\Validators\Rules;

use Framework\Core\Validators\Rules\ValidationRuleBase;

/**
 * Checks to see if the left hand side value is exactly the same as the right hand side value passed.
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
class EqualRule extends ValidationRuleBase {

    /**
     * Stores the left hand side value to be checked with the right hand side value.
     */
    private $_leftValue = null;

    /**
     * Stores the right hand side value to be checked with the left hand side value.
     */
    private $_rightValue = null;

    /**
     * Initializes a new instance of the EqualRule validation class with the error message to be returned to the user.
     * 
     * @param mixed $leftValue Indicates the left hand side value to be checked with the right hand side value.
     * @param mixed $rightValue Indicates the right hand side value to be checked with the left hand side value.
     * @param string $errorMessage The actual error message the user sees if validation fails.
     */
    public function __construct($leftValue, $rightValue, string $errorMessage = 'does not match because "%s" and "%s" are not exactly the same.') {
        $this->_leftValue = $leftValue;
        $this->_rightValue = $rightValue;
        $this->errorMessage = sprintf($errorMessage, $leftValue, $rightValue);
    }

    /**
     * Checks to see if the validation succeed or not and returns a boolean value, indicating the status of the validation.
     * 
     * @param mixed $value The actual value to be validated.
     * 
     * @return bool
     */
    function isValid($value = null) : bool {
        return $this->_leftValue === $this->_rightValue;
    }

}