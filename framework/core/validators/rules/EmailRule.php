<?php

namespace Framework\Core\Validators\Rules;

use Framework\Core\Validators\Rules\ValidationRuleBase;

/**
 * Checks to see if the value passed is a valid email. Note that if the value passed is empty, it will return true.
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
class EmailRule extends ValidationRuleBase {

    /**
     * Initializes a new instance of the EmailRule validation class with the error message to be returned to the user.
     * 
     * @param string $errorMessage The actual error message the user sees if validation fails.
     */
    public function __construct(string $errorMessage = 'is not a valid email.') {
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
        if (!$value) return true;
        if (mb_strlen(trim($value)) == 0) return true;        
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

}