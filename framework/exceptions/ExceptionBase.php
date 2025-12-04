<?php

namespace Framework\Exceptions;

use Exception;
use Framework\Interfaces\IExceptionBase;

class ExceptionBase extends Exception implements IExceptionBase {

    public static function convertExceptionToStringForLogging(Exception $exception) {
        $error = 'Error message: ' . $exception->getMessage() . PHP_EOL . 
            'Line number: ' . strval($exception->getLine()) . PHP_EOL . 
            'File: ' . $exception->getFile() . PHP_EOL . 
            'Stack trace: ' . $exception->getTraceAsString() . PHP_EOL;

        return $error;
    }

    public static function getErrorMessage(Exception $exception) : string {
        $error = (IS_DEVELOPMENT ? $exception->getMessage() : USER_FRIENDLY_ERROR_MESSAGE);
        return $error;
    }

}