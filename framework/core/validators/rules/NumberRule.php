<?php

namespace Framework\Core\Validators\Rules;

use Framework\Core\Validators\Rules\ValidationRuleBase;

/**
 * Checks to see if the value passed is a valid numeric value.
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
class NumberRule extends ValidationRuleBase {

    /**
     * Initializes a new NumberRule of the RequireRule validation class with the error message to be returned to the user.
     * 
     * @param string $errorMessage The actual error message the user sees if validation fails.
     */
    public function __construct(string $errorMessage = 'value is not a valid number..') {
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
        return is_numeric($value);
    }
}