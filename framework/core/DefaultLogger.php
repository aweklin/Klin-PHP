<?php

use Framework\Enums\LogLevel;
use Framework\Interfaces\ILogger;
use Framework\Utils\Date;
use Framework\Utils\File;

/**
 * This is the default logger class for logging messages in the app.
 * 
 * @author Akeem Aweda |
 */
final class DefaultLogger implements ILogger {
    public function log(LogLevel $logLevel, $text) {
        if ($text) {
            $error = PHP_EOL 
              . "[" . $logLevel->value . "] " 
              . Date::now() 
              . PHP_EOL 
              . $text 
              . PHP_EOL 
              . '=========================================================' 
              . PHP_EOL;
            $fileName = PATH_APP_TMP_LOGS 
              . DS 
              . $logLevel->value 
              . '_' 
              . Date::now(Date::FORMAT_YMD);
            File::write($fileName, $error);
        }
    }

    public function debug($text) {
        $this->log(LogLevel::DEBUG, $text);
    }

    public function error($text) {
        $this->log(LogLevel::ERROR, $text);
    }
    
    public function warning($text) {
        $this->log(LogLevel::WARNING, $text);
    }
    
    public function info($text) {
        $this->log(LogLevel::INFO, $text);
    }
}