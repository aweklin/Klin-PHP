<?php

namespace Framework\Core\Validators\Rules;

use Framework\Core\Model;
use Framework\Core\Validators\Rules\ValidationRuleBase;
use Framework\Utils\Str;

/**
 * Checks to see if the value passed already exists in the database.
 * If you passed false to $isInserting parameter, you must specify the primary field value.
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
class UniqueRule extends ValidationRuleBase {

    private $_isInserting = true;
    private $_model;
    private $_fieldToCheck = '';
    private $_primaryFieldValue = '';

    /**
     * Initializes a new UniqueRule validation class with the error message to be returned to the user.
     * 
     * @param \Framework\Core\Model $model Indicate a child class of the Model class.
     * @param string $fieldToCheck Indicates the field to check for uniqueness.
     * @param bool Indicates whether you are checking uniqueness for new record or existing record.
     * @param string $primaryFieldValue Indicates the value of the primary field. If you passed false to $isInserting parameter, you must specify the primary field value.
     * @param string $errorMessage The actual error message the user sees if validation fails.
     */
    public function __construct(Model $model, string $fieldToCheck, bool $isInserting = true, string $primaryFieldValue = '', string $errorMessage = 'value: %s already exists.') {
        $this->_model = $model;
        $this->_fieldToCheck = $fieldToCheck;
        $this->_isInserting = $isInserting;
        $this->_primaryFieldValue = $primaryFieldValue;
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
        if (Str::isEmpty($this->_fieldToCheck)) {
            $this->errorMessage = 'unique check requires that you set the field to check for uniqueness.';
            return false;
        }
        if (!$this->_isInserting && Str::isEmpty($this->_primaryFieldValue)) {
            $this->errorMessage = 'unique check requires that you set the primary field value.';
            return false;
        }
        
        $this->errorMessage = sprintf($this->errorMessage, $value);
        if (!$this->_isInserting) {
            return 
                $this->_model->where($this->_model->getPrimaryField(), '!=', $this->_primaryFieldValue)
                            ->_and()
                            ->where($this->_fieldToCheck, '=', $value)
                            ->count() == 0;
        } else {
            return $this->_model->where($this->_fieldToCheck, '=', $value)->count() == 0;
        }
    }
}