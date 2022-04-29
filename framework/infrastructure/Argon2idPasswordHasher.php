<?php

namespace Framework\Infrastructure;

use Framework\Decorator\PasswordEncryptor;
use IPasswordVerifier;

class Argon2idPasswordHasher extends PasswordEncryptor implements IPasswordVerifier {

    private int $_memoryCost;
    private int $_timeCost;
    private int $_threads;
    public function __construct(
        int $memoryCost = PASSWORD_ARGON2_DEFAULT_MEMORY_COST, 
        int $timeCost = PASSWORD_ARGON2_DEFAULT_TIME_COST, 
        int $threads = PASSWORD_ARGON2_DEFAULT_THREADS
    ) {
        $this->_memoryCost = $memoryCost;
        $this->_timeCost = $timeCost;
        $this->_threads = $threads;
    }

    function encrypt(string $password): string {
        $this->_validatePasswordForEncryption($password);

        return password_hash(
            $password, 
            PASSWORD_ARGON2ID, 
            [
                'memory_cost' => $this->_memoryCost, 
                'time_cost' => $this->_timeCost, 
                'threads' => $this->_threads
            ]
        );
    }


    public function isVerified(string $password, string $hashed) : bool {
        $this->_validatePasswordForEncryption($hashed, 'Hashed password');

        return password_verify($password, $hashed);
    }

}