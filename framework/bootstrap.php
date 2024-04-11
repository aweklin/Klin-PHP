<?php

use Framework\Libs\Inflection;
use Framework\Utils\Date;
use Framework\Core\App;
use Framework\Infrastructure\Cookie;
use App\Src\Models\User;
use Framework\Core\Json;
use Framework\Core\Response;
use Framework\Core\Router;
use Framework\Infrastructure\DependencyContainer;
use Framework\Infrastructure\ErrorLogger;
use Framework\Interfaces\IJson;
use Framework\Interfaces\ILogger;
use Framework\Interfaces\IResponse;

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

// statutory dependencies registration
$dependencyContainer->register(ILogger::class, ErrorLogger::class);
$dependencyContainer->register(IResponse::class, Response::class);
$dependencyContainer->register(IJson::class, Json::class);

// user specific dependencies registration
require_once (PATH_APP_CONFIG . DS . 'dependencies.php');

// These two lines are for inflection purpose only to inflect class names.
$inflection = new Inflection();

Date::setTimeZone(TIME_ZONE);

if (isset($requestUrl)) {
    // initialize app and route request
    $router = new Router($dependencyContainer);
    new App($router, $requestUrl);
}