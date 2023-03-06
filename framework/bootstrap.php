<?php

use Framework\Libs\Inflection;
use Framework\Utils\Date;
use Framework\Core\App;
use Framework\Infrastructure\Cookie;
use App\Src\Models\User;
use Framework\Core\Router;
use Framework\Infrastructure\DependencyContainer;
use Framework\Infrastructure\ErrorLogger;
use Framework\Interfaces\ILogger;

/**
 * 
 * This file bootstraps the app, it loads classes as required and also responsible for routing requests.
 * 
 */

// ensure the configuration files required are present
if (!file_exists(PATH_APP . DS . 'constants.php')) {
    die('A configuration file is needed inside the \"' . PATH_APP . '\" directory. The file name must be \"constants.php\".');
}
if (!file_exists(PATH_APP_CONFIG . DS . 'dependencies.php')) {
    die('A configuration file is needed inside the \"' . PATH_APP_CONFIG . DS . '\" directory. The file name must be \"dependencies.php\".');
}
if (!file_exists(PATH_APP_CONFIG . DS . 'database.php')) {
    die('A configuration file is needed inside the \"' . PATH_APP_CONFIG . DS . '\" directory. The file name must be \"database.php\".');
}
if (!file_exists(PATH_APP_CONFIG . DS . 'mail.php')) {
    die('A configuration file is needed inside the \"' . PATH_APP_CONFIG . DS . '\" directory. The file name must be \"mail.php\".');
}
if (!file_exists(PATH_APP_CONFIG . DS . 'urls.php')) {
    die('A configuration file is needed inside the \"' . PATH_APP_CONFIG . DS . '\" directory. The file name must be \"urls.php\".');
}
if (!file_exists(PATH_APP_CONFIG . DS . 'model.php')) {
    die('A configuration file is needed inside the \"' . PATH_APP_CONFIG . DS . '\" directory. The file name must be \"model.php\".');
}
if (!file_exists(PATH_APP_CONFIG . DS . 'controller.php')) {
    die('A configuration file is needed inside the \"' . PATH_APP_CONFIG . DS . '\" directory. The file name must be \"controller.php\".');
}
if (!file_exists(PATH_APP_CONFIG . DS . 'security.php')) {
    die('A configuration file is needed inside the \"' . PATH_APP_CONFIG . DS . '\" directory. The file name must be \"security.php\".');
}

// include configuration and helper functions files
require_once (PATH_APP . DS . 'constants.php');
require_once (PATH_APP_CONFIG . DS . 'urls.php');
require_once (PATH_APP_CONFIG . DS . 'database.php');
require_once (PATH_APP_CONFIG . DS . 'model.php');
require_once (PATH_APP_CONFIG . DS . 'controller.php');
require_once (PATH_APP_CONFIG . DS . 'mail.php');
require_once (PATH_APP_CONFIG . DS . 'security.php');

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

$dependencyContainer = new DependencyContainer();

$dependencyContainer->register(ILogger::class, ErrorLogger::class);
require_once (PATH_APP_CONFIG . DS . 'dependencies.php');

// These two lines are for inflection purpose only to inflect class names.
$inflection = new Inflection();

Date::setTimeZone(TIME_ZONE);

// login user from cookie
if (Cookie::exists(SECURITY_COOKIE_REMEMBER_ME_NAME)) {
    User::loginFromCookie();
}

if (isset($requestUrl)) {
    // initialize app and route request
    $router = new Router($dependencyContainer);
    new App($router, $requestUrl);
}