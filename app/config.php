<?php

/**
 * Welcome to KlinPHP version 0.1 (Preview), a clean, robust & secure PHP MVC framework.
 * 
 * This project is an effort by Akeem Aweda. A project created to serve as a Rapic Application Development for PHP applications.
 * It's been a good journey since 2014. It's been refined, redesigned from time to time to leverage on current programming model, 
 * best practices, scalability, and security.
 * 
 * PLEASE, DO NOT DELETE THESE COMMENTS SINCE THIS PROJECT IS INTENTED TO BE OPEN SOURCE. 
 * IT'S A WAY OF APPRECIATING THE CREATOR OF THIS SMALL MVC FRAMEWORK.
 * IF YOU WOULD LIKE TO MAKE SOME DONATIONS, YOU CAN CONTACT ME ON THE EMAIL OR PHONE NO BELOW.
 * 
 * WARNING!!!
 * DO NOT REMOVE ANY OF THE CONSTANTS HERE. ONLY MODIFY THE VALUE OF THE CONSTANT AS NEEDED
 * DELETING ANY CONSTANT DEFINED HERE MAY RESULT IN THE APP NOT WORKING AS EXPECTED
 * 
 * @copyright Akeem Aweda | akeem@aweklin.com | +2347085287169
 * @copyright All rights reserved.
 */
 
$appDevelopmentDirectoryName = 'klinPHP';   // change this to the current app folder name

// indicates whether your app is running in development or production.
define('IS_DEVELOPMENT', true);


// inflection assistant
$irregularWords = ['class' => 'classes'];   // you can add as many irregular words as needed


// timezone
define('TIME_ZONE', 'GMT+1');


// default user friendly message that is shown to the user when unexpected server occurrs.
define('USER_FRIENDLY_ERROR_MESSAGE', 'An internal server error ocurred while processing your request.');

// If you have not added another folder to the app directory,
// use this to specify paths or your custom classes and/or libraries to be autoloaded.
// Note that you can prefix some of the constants already defined globally here.
// For example, PATH_APP_LIBS . DS . 'library_folder_name'.
// List of your paths should just be separated with comma. No need to make it associative array.
define('PATH_CUSTOM_OR_LIBRARY_CLASSES', []);


// urls
define('APP_BASE_URL', (IS_DEVELOPMENT ? '/' . $appDevelopmentDirectoryName. '/' : 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}/"));
define('URL_PUBLIC', APP_BASE_URL . 'app/public/');
define('URL_PUBLIC_JS', URL_PUBLIC . 'js/');
define('URL_PUBLIC_CSS', URL_PUBLIC . 'css/');
define('URL_PUBLIC_FONTS', URL_PUBLIC . 'fonts/');
define('URL_PUBLIC_IMG', URL_PUBLIC . 'img/');


// layout constants
define('SITE_TITLE', 'Autism Tracker - Admin Console');
define('LAYOUT_DEFAULT', 'layout');
define('APP_MESSAGE', $appDevelopmentDirectoryName . 'AppMessage');
define('APP_MESSAGE_TYPE', $appDevelopmentDirectoryName . 'AppMessageType');


// controller constants
define('CONTROLLER_SUFFIX', 'Controller');
define('DEFAULT_CONTROLLER', 'Account');
define('DEFAULT_ACTION', 'login');


// database configuration
define('DATABASE_HOST', (IS_DEVELOPMENT ? '127.0.0.1' : ''));
define('DATABASE_NAME', (IS_DEVELOPMENT ? '' : ''));
define('DATABASE_USER', (IS_DEVELOPMENT ? '' : ''));
define('DATABASE_PASSWORD', (IS_DEVELOPMENT ? '' : ''));
define('DATABASE_TABLE_NAMES_PLURALIZED', true);


// email configuration
define('EMAIL_HOST', '');
define('EMAIL_HOST_PORT', '');
define('EMAIL_USERNAME', '');
define('EMAIL_PASSWORD', '');
define('EMAIL_DEFAULT_SENDER_EMAIL', '');
define('EMAIL_DEFAULT_SENDER_NAME', '');
define('EMAIL_DEFAULT_CC', []);
define('EMAIL_DEFAULT_BCC', []);
define('EMAIL_HOST_SECURITY_TYPE', 'ssl');


// security configurations
define('SECURITY_CURRENT_LOGGED_IN_USER_ID', str_replace(' ', '', $appDevelopmentDirectoryName) . 'S3ssi0nN@me');
define('SECURITY_COOKIE_REMEMBER_ME_NAME', str_replace(' ', '', $appDevelopmentDirectoryName) . 'C0oki3N@me');
define('SECURITY_COOKIE_EXPIRY', 2592000);   // expires after 30 days
define('SECURITY_FORM_TOKEN', 'form_token');


// file upload
define('UPLOAD_ALLOWED_EXTENSIONS', ['jpg', 'png', 'jpeg']);


/******************** everything else can be defined here ***********************/
define('OPERATION_SUCCEEDED', 'Operation succeeded.');
define('ALL_FIELD_ARE_REQUIRED', 'All fields are required.');
define('INVALID_REQUEST', 'Invalid request.');