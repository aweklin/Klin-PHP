<?php

// ensure the user is running a supported PHP version
$supportedVersion = 8.1;
if (PHP_VERSION < $supportedVersion) {
    die('You are currently running PHP version \"' . PHP_VERSION . '\". This app works only with PHP ' . $supportedVersion . ' and above.');
}

// define global constants that it used throughout the app
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));

// get the request url details (convert it to array so controller, action, and parameters can be extracted later)
$requestUrl = (isset($_SERVER['PATH_INFO']) ? explode('/', filter_var(ltrim($_SERVER['PATH_INFO'], '/'), FILTER_SANITIZE_URL)) : []);

// include global constant file
require_once(ROOT . DS . 'framework' . DS . 'constants.php');

// start session
session_start();

// bootstrap the app
require_once(ROOT . DS . 'framework' . DS . 'bootstrap.php');

function convertExceptionToStringForLogging(Exception $exception) : string {
    $error = 'Error message: ' . $exception->getMessage() . PHP_EOL . 
        'Line number: ' . strval($exception->getLine()) . PHP_EOL . 
        'File: ' . $exception->getFile() . PHP_EOL . 
        'Stack trace: ' . $exception->getTraceAsString() . PHP_EOL;

    return $error;
}

function getErrorMessage(Exception $exception) : string {
    $error = (IS_DEVELOPMENT ? $exception->getMessage() : USER_FRIENDLY_ERROR_MESSAGE);
    return $error;
}