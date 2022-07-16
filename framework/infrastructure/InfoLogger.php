<?php

namespace Framework\Infrastructure;

use Framework\Decorator\Logger;
use Framework\Utils\{Date, File};

/**
 * This is a wrapper class for logging errors in the app.
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
class InfoLogger extends Logger {

    /**
     * Logs an information to a text file, located in the app/tmp/logs directory, using the current date (only) as the file name. 
     * This means all information for the day are logged to a single information log file.
     * 
     * @param string $text The information information to log.
     * 
     * @return void
     */
    public function log($text) {
        if ($text) {
            $info = PHP_EOL . Date::now() . PHP_EOL . $text . PHP_EOL . '=========================================================' . PHP_EOL;
            $fileName = PATH_APP_TMP_LOGS . DS . 'info_' . Date::now(Date::FORMAT_YMD);
            File::write($fileName, $info);
        }
    }

}