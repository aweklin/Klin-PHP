<?php

namespace Framework\Exceptions;

class InvalidRequestException extends ExceptionBase {
    public function __construct() {
        parent::__construct('This request is not valid.');
    }
}