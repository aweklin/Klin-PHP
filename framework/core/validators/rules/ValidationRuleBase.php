<?php

namespace Framework\Core\Validators\Rules;

use Framework\Interfaces\IValidationRule;

/**
 * Base class for all validation rules.
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
abstract class ValidationRuleBase implements IValidationRule {
    /**
     * Stores the error message returned by a validation rule.
     */
    protected $errorMessage = '';

    /**
     * Indicates whether the validation passed required validation rule.
     * It is true by default because we may not require email but check for the value entered.
     */
    protected $passedRequiredRule = true;

    /**
     * Indicates whether the current validation rule is a numeric check validation.
     */
    protected $isNumberValidation = false;

    /**
     * Checks to see if the validation succeed or not and returns a boolean value, indicating the status of the validation.
     * This method must be implemented by the sub class.
     * 
     * @param mixed $value The actual value to be validated.
     * 
     * @return bool
     */
    abstract function isValid($value = null) : bool;
    
    /**
     * Retrieves the error message from the validation. If validation was not done, an empty string is returned.
     * 
     * @return string
     */
    function getErrorMessage() : string {
        return $this->errorMessage;
    }

    /**
     * Checks if the value passed matches the regular expression pattern.
     * 
     * @param string $pattern Regular expression pattern.
     * @param mixed $value Value being checked for pattern matching.
     * 
     * @return bool
     */
    protected function isValidExpression(string $pattern, $value) : bool {
        return filter_var($value, FILTER_VALIDATE_REGEXP, ["options"=> ["regexp"=>'!' . $pattern .'!i']]) !== false;
    }
}