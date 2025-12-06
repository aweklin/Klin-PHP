<?php

namespace Framework\Core\Validators\Rules;

/**
 * Checks the given password against some rules you provided
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
final class StrongPasswordRule extends RequiredRule {
  
  /**
   * Initializes a new instance of the RequireRule validation class with the error message to be returned to the user.
   * 
   * @param string $errorMessage The actual error message the user sees if validation fails.
   */
  public function __construct(readonly int $minimumLength) {
    $this->errorMessage = '';
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

    $password = trim($value);

    $result = new PasswordValidationResult(
        strlen($password) >= $this->minimumLength,
        preg_match('/[A-Z]/', $password) === 1,
        preg_match('/[a-z]/', $password) === 1,
        preg_match('/[0-9]/', $password) === 1,
        preg_match('/[^a-zA-Z0-9]/', $password) === 1
    );

    $isValid = $result->isValid();

    $this->errorMessage = sprintf('must be at least "%u" characters long, contain a capital letter, small letter and special character', $this->minimumLength);

    return $isValid;
  }
}

class PasswordValidationResult {
    public bool $hasMinLength;
    public bool $hasUppercase;
    public bool $hasLowercase;
    public bool $hasNumber;
    public bool $hasSpecialChar;

    public function __construct(
        bool $hasMinLength = false,
        bool $hasUppercase = false,
        bool $hasLowercase = false,
        bool $hasNumber = false,
        bool $hasSpecialChar = false
    ) {
        $this->hasMinLength   = $hasMinLength;
        $this->hasUppercase   = $hasUppercase;
        $this->hasLowercase   = $hasLowercase;
        $this->hasNumber      = $hasNumber;
        $this->hasSpecialChar = $hasSpecialChar;
    }

    public function isValid(): bool {
        return $this->hasMinLength
            && $this->hasUppercase
            && $this->hasLowercase
            && $this->hasNumber
            && $this->hasSpecialChar;
    }
}