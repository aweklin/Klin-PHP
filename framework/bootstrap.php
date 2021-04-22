<?php

use Framework\Libs\Inflection;
use Framework\Utils\Date;
use Framework\Core\App;
use Framework\Infrastructure\Cookie;
use App\Src\Models\User;

/**
 * 
 * This file bootstraps the app, it loads classes as required and also responsible for routing requests.
 * 
 */

// ensure the configuration file is present
if (!file_exists(PATH_APP . DS . 'config.php')) {
    die('A configuration file is needed inside the \"' . PATH_APP . '\" directory. The file name must be \"config.php\".');
}

// include configuration and helper functions files
require_once (PATH_APP . DS . 'config.php');

// autoload classes with anonymous function
spl_autoload_register(function($className) {

    $classArray = explode('\\', $className);
    $class = array_pop($classArray);
    $subPath = mb_strtolower(implode(DS, $classArray));

    $classPath = ROOT . DS . $subPath . DS . $class . '.php';
    if (file_exists($classPath)) {
        include_once ($classPath);
        return;
    }

});

// These two lines are for inflection purpose only to inflect class names.
$inflection = new Inflection();

Date::setTimeZone(TIME_ZONE);

// login user from cookie
if (Cookie::exists(SECURITY_COOKIE_REMEMBER_ME_NAME)) {
    User::loginFromCookie();
}

if (isset($requestUrl)) {
    // initialize app and route request
    new App($requestUrl);
}