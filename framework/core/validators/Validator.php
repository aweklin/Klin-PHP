<?php

namespace Framework\Core\Validators;

/**
 * Contains the logic for checking all validation errors based on the validation rules passed to the constructor.
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
class Validator {

    /**
     * Contains all the validation logic to be checked.
     */
    private $_validations = [];
    /**
     * Stores all the validation errors.
     */
    private $_validationErrors = [];

    /**
     * Initializes new validation object with the list of all validations to be done.
     * 
     * @param array $validations An array of validations to be done. Example [new Validation('Username', $value, [new RequiredRule()])].
     */
    public function __construct(array $validations) {
        $this->_validations = $validations;
    }

    /**
     * Runs all validation rules passed to the constructor and returns a value, indicating the success or failure of all validations done.
     */
    public function isValid() : bool {
        $this->_validationErrors = [];

        if (!$this->_validations) return true;

        foreach($this->_validations as $validation) {
            foreach($validation->getRules() as $rule) {
                if (!$rule->isValid($validation->getValue())) {
                    array_push($this->_validationErrors, $validation->getField() . " " . $rule->getErrorMessage());
                }
            }
        }

        return (count($this->_validationErrors) == 0);
    }

    /**
     * Returns all error messages from the validation as unordered list. You can modify the css validation-error class to suite your need.
     * 
     * @return string
     */
    function getValidationErrors() : string {
        if (count($this->_validationErrors) == 0) return '';

        $errors = '<ul class="validation-errors">';
        foreach ($this->_validationErrors as $errorMessage) {
            $errors .= PHP_EOL . "<li>{$errorMessage}</li>";
        }
        $errors .= '</ul>';

        return $errors;
    }

}