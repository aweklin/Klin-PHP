<?php

namespace Framework\Core\Validators\Rules;

use \DateTime;
use Framework\Core\Validators\Rules\ValidationRuleBase;
use Framework\Utils\Str;

/**
 * Checks to see if the value passed is a valid date. 
 * Note that if the value passed is empty, it will return true.
 * Also note that the date parsed must be in the format Y-m-d or Y/m/d.
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
class DateRule extends ValidationRuleBase {

    /**
     * Initializes a new instance of the DateRule validation class with the error message to be returned to the user.
     * 
     * @param string $errorMessage The actual error message the user sees if validation fails.
     */
    public function __construct(string $errorMessage = '%s is not a valid date') {
        $this->errorMessage = $errorMessage;
    }

    /**
     * Checks to see if the validation succeed or not and returns a boolean value, indicating the status of the validation.
     * 
     * @param mixed $value The actual value to be validated.
     * 
     * @return bool
     */
    public function isValid($value = null) : bool {
        if (Str::isEmpty($value)) return true;
        $this->errorMessage = sprintf($this->errorMessage, $value);

        // credits: https://stackoverflow.com/questions/13194322/php-regex-to-check-date-is-in-yyyy-mm-dd-format#answer-13194398
        $dt = DateTime::createFromFormat("Y-m-d", str_replace('/', '-', $value));
        return $dt !== false && !array_sum($dt::getLastErrors());
    }
}