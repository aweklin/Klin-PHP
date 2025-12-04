<?php

namespace Framework\Interfaces;

use Framework\Enums\LogLevel;

/**
 * Wraps methods for logging messages in the app.
 */
interface ILogger {
    /**
     * Logs a message to the log file.
     * 
     * @param LogLevel $logLevel Specifies the level of the log.
     * @param mixed $text Specifies the text or message to be logged.
     * 
     * @return void
     */
    public function log(LogLevel $logLevel, $text);

    /**
     * Logs a debug message to the log file.
     * 
     * @param mixed $text Specifies the text or message to be logged.
     * 
     * @return void
     */
    public function debug($text);

    /**
     * Logs an error message to the log file.
     * 
     * @param mixed $text Specifies the text or message to be logged.
     * 
     * @return void
     */
    public function error($text);

    /**
    * Logs a warning message to the log file.
    * 
    * @param mixed $text Specifies the text or message to be logged.
    * 
    * @return void
    */
    public function warning($text);

    /**
    * Logs a info message to the log file.
    * 
    * @param mixed $text Specifies the text or message to be logged.
    * 
    * @return void
    */
    public function info($text);
}