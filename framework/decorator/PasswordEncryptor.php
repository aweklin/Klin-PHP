<?php

namespace Framework\Decorator;

use Framework\Exceptions\InvalidOperationException;
use Framework\Interfaces\IPasswordEncryptor;
use Framework\Interfaces\IPasswordVerifier;
use Framework\Utils\Str;
use InvalidArgumentException;

/**
 * The base class for password encryption.
*/
abstract class PasswordEncryptor {
    
    /** 
     * Encapsulates logic for password encryption.
    */
    protected IPasswordEncryptor $passwordEncryptor;

    /**
     * Called to run some validations on the password to has before proceeding with the encryption.
     * For example, this method will throw an InvalidArgumentException if the given password is empty.
     */
    protected function _validatePasswordForEncryption(string $password, string $fieldName = 'Password') {
        if (Str::isEmpty($password))
            throw new InvalidArgumentException("$fieldName is required.");    
    }

    /**
     * Generates a hash password based on the algorithm provided
    */
    public abstract function encrypt(string $password): string;    

    public function toVerifier(): IPasswordVerifier {
        if ($this instanceof IPasswordVerifier) return $this;

        throw new InvalidOperationException("Unable to cast to IPasswordVerifier instance");
    }
}