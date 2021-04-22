<?php

namespace Framework\Core\Validators\Rules;

use Framework\Core\Validators\Rules\NumberRule;

/**
 * Checks to see if the value passed is between the given lower and upper bound.
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
class BetweenValuesRule extends NumberRule {
    /**
     * Stores the minimum value needed to pass validation.
     */
    private $_minValue = 0;
    /**
     * Stores the maximum value needed to pass validation.
     */
    private $_maxValue = 0;

    /**
     * Initializes a new instance of the MaximumValueRule validation class with the error message to be returned to the user.
     * 
     * @param double $value Indicates the maximum value required
     * @param string $errorMessage The actual error message the user sees if validation fails.
     */
    public function __construct($minValue, $maxValue, string $errorMessage = 'is not a valid number.') {
        parent::__construct($errorMessage);
        $this->_minValue = $minValue;
        $this->_maxValue = $maxValue;
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
        
        $this->errorMessage = sprintf('value: "%u" must be in between "%u" and "%u"', $value, $this->_minValue, $this->_maxValue);
        return doubleval($value) >= doubleval($this->_minValue) && doubleval($value) <= doubleval($this->_maxValue);
    }
}