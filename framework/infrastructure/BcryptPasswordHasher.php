<?php

namespace Framework\Infrastructure;

use Framework\Decorator\PasswordEncryptor;
use Framework\Interfaces\IPasswordVerifier;

class BcryptPasswordHasher extends PasswordEncryptor implements IPasswordVerifier {

    private int $_cost;

    public function __construct(int $cost = 10) {
        $this->_cost = $cost;
    }


    public function encrypt(string $password): string {
        $this->_validatePasswordForEncryption($password);

        return password_hash($password, PASSWORD_BCRYPT, ['cost' => $this->_cost]);
    }

    public function isVerified(string $password, string $hashed) : bool {
        $this->_validatePasswordForEncryption($hashed, 'Hashed password');

        return password_verify($password, $hashed);
    }

}