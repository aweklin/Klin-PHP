<?php

namespace Framework\Infrastructure;

use Framework\Utils\{File, Date};
use Framework\Decorator\Logger;

/**
 * This is a wrapper class for logging errors in the app.
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
final class ErrorLogger extends Logger {

    /**
     * Logs an error to a text file, located in the app/tmp/logs directory, using the current date (only) as the file name. 
     * This means all errors that ocurred in a day are logged to a single error log file.
     * 
     * @param string $text The error information to log.
     * 
     * @return void
     */
    public function log($text) {
        
        if ($text) {
            $error = PHP_EOL . Date::now() . PHP_EOL . $text . PHP_EOL . '=========================================================' . PHP_EOL;
            $fileName = PATH_APP_TMP_LOGS . DS . 'error_' . Date::now(Date::FORMAT_YMD);
            File::write($fileName, $error);
        }

    }

}