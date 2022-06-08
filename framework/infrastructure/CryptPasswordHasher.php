<?php

namespace Framework\Infrastructure;

use Framework\Decorator\PasswordEncryptor;
use Framework\Interfaces\IPasswordVerifier;

class CryptPasswordHasher extends PasswordEncryptor implements IPasswordVerifier {

    private string $_salt;
    public function __construct(string $salt = '') {
        $this->_salt = $salt;
    }

    function encrypt(string $password): string {
        $this->_validatePasswordForEncryption($password);

        return crypt($password, $this->_salt);
    }

    public function isVerified(string $password, string $hashed) : bool {
        $this->_validatePasswordForEncryption($hashed, 'Hashed password');

        return password_verify($password, $hashed);
    }

}