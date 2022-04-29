<?php

/**
 * Provides the mechanisms to verify a given password.
*/
interface IPasswordVerifier {
    /**
     * Allows verification of a given password by comparing the plain password with the provided hashed counterpart.
    */
    function isVerified(string $password, string $hashed) : bool;
}