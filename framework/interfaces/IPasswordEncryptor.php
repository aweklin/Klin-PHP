<?php

namespace Framework\Interfaces;

interface IPasswordEncryptor {
    function encrypt(string $password) : string;
}