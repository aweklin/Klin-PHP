<?php

namespace Framework\Core\Validators;

/**
 * Wraps the validation rule with caption/field to be validated, the value to be validated and the set of rules for this validation.
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
class ValidationRule {
    private $_field = '';
    private $_value = null;
    private $_rules = [];

    /**
     * Initializes a new instance of the ValidationRule class with caption/field to be validated, the value to be validated and the set of rules for this validation.
     * 
     * @param string $field This is the label prefixed to each validation error message returned. Example can be Username.
     * @param mixed $value The actual value to be validated against the set of rules passed.
     * @param array $rules Defines all the validation rules to be run.
     */
    public function __construct(string $field, $value = null, array $rules = []) {
        $this->_field = $field;
        $this->_value = $value;
        $this->_rules = $rules;
    }

    /**
     * Returns the label or field name being validated.
     * 
     * @return string
     */
    public function getField() : string {
        return $this->_field;
    }

    /**
     * Returns the value to be check for validation.
     * 
     * @return mixed
     */
    public function getValue() {
        return $this->_value;
    }

    /**
     * Returns the list of all validation rules to be run.
     * 
     * @return array
     */
    public function getRules() : array {
        return $this->_rules;
    }
}