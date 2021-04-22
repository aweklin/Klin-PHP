<?php

namespace Framework\Core\Validators\Rules;

use Framework\Core\Validators\Rules\ValidationRuleBase;

/**
 * Checks to see if the value passed is not null and not empty. This means it must have at least, an alphabet or numeric value.
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
class RequiredRule extends ValidationRuleBase {

    /**
     * Initializes a new instance of the RequireRule validation class with the error message to be returned to the user.
     * 
     * @param string $errorMessage The actual error message the user sees if validation fails.
     */
    public function __construct(string $errorMessage = 'is required') {
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
        return $this->isValidExpression('[a-z0-9A-Z]+', $value);
    }
}