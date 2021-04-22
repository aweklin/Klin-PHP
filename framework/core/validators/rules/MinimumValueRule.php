<?php

namespace Framework\Core\Validators\Rules;

use Framework\Core\Validators\Rules\NumberRule;

/**
 * Checks to see if the value passed is less than the expected value.
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
class MinimumValueRule extends NumberRule {
    /**
     * Stores the minimum value needed to pass validation.
     */
    private $_value = 0;

    /**
     * Initializes a new instance of the MinimumValueRule validation class with the error message to be returned to the user.
     * 
     * @param double $value Indicates the minimum value required
     * @param string $errorMessage The actual error message the user sees if validation fails.
     */
    public function __construct($value, string $errorMessage = 'is not a valid number.') {
        parent::__construct($errorMessage);
        $this->_value = $value;        
    }

    /**
     * Checks to see if the validation succeed or not and returns a boolean value, indicating the status of the validation.
     * 
     * @param mixed $value The actual value to be validated.
     * 
     * @return bool
     */
    function isValid($value = null) : bool {
        if (!parent::isValid($value)) return true;
        
        $this->errorMessage = sprintf('value: "%u" cannot be less than "%u"', $value, $this->_value);
        return doubleval($value) >= doubleval($this->_value);
    }
}