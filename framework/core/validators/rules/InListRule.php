<?php

namespace Framework\Core\Validators\Rules;

use Framework\Core\Validators\Rules\NumberRule;
use Framework\Utils\{Ary, Str};

/**
 * Checks to see if the value passed is in the array list list being validated. Note that if the value passed is empty, it will return true.
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
class InListRule extends RequiredRule {

    /**
     * Stores the key to be used to search the list if it appears to be an associative array.
     */
    private $_arrayKey = '';
    /**
     * Stores the list of all valid items to be checked.
     */
    private $_list = [];

    /**
     * Initializes a new instance of the MaximumValueRule validation class with the error message to be returned to the user.
     * 
     * @param array $list Indicates the array to be checked a value against.
     * @param string $arrayKey If the list given is an associative array, you must specify the key to be used for this validation.
     * @param string $errorMessage The actual error message the user sees if validation fails.
     */
    public function __construct(array $list, string $arrayKey = '', string $errorMessage = 'is required') {
        parent::__construct($errorMessage);
        $this->_arrayKey = $arrayKey;
        $this->_list = $list;
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
        if (count($this->_list) == 0) return true;
        if (Ary::isAssociative($this->_list) && Str::isEmpty($this->_arrayKey)) {
            $this->errorMessage = 'must specify a key since associative array is being validated.';
            return false;
        }

        // compose list of values in the array as this would be used for the result check.
        $listValues = [];
        foreach($this->_list as $item) {
            if (is_object($item)) {
                array_push($listValues, $item->{$this->_arrayKey});
            } else {
                if (is_array($item)) {
                    array_push($listValues, $item[$this->_arrayKey]);
                } else {
                    array_push($listValues, $item);
                }
            }
        }

        $this->errorMessage = sprintf('value: "%u" must be one of %s', $value, implode(', ', $listValues));
        return in_array($value, $listValues);
    }
}