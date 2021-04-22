<?php

/**
 * 
 * This file contains global constants that it used throughout the app
 * 
 */

/********************** global paths **********************/

// framework paths
define('PATH_FRAMEWORK', ROOT . DS . 'framework');
define('PATH_FRAMEWORK_CORE', PATH_FRAMEWORK . DS . 'core');
define('PATH_FRAMEWORK_CORE_VALIDATORS', PATH_FRAMEWORK_CORE . DS . 'validators');
define('PATH_FRAMEWORK_CORE_VALIDATORS_RULES', PATH_FRAMEWORK_CORE_VALIDATORS . DS . 'rules');
define('PATH_FRAMEWORK_UTILS', PATH_FRAMEWORK . DS . 'utils');
define('PATH_FRAMEWORK_LIBS', PATH_FRAMEWORK . DS . 'libs');
define('PATH_FRAMEWORK_INTERFACES', PATH_FRAMEWORK . DS . 'interfaces');
define('PATH_FRAMEWORK_DECORATOR', PATH_FRAMEWORK . DS . 'decorator');
define('PATH_FRAMEWORK_INFRASTRUCTURE', PATH_FRAMEWORK . DS . 'infrastructure');

// app paths
define('PATH_APP', ROOT . DS . 'app');
define('PATH_APP_TMP', PATH_APP . DS . 'tmp');
define('PATH_APP_TMP_LOGS', PATH_APP_TMP . DS . 'logs');
define('PATH_APP_TMP_CACHE', PATH_APP_TMP . DS . 'cache');
define('PATH_APP_SRC', PATH_APP . DS . 'src');
define('PATH_APP_CONTROLLERS', PATH_APP_SRC . DS . 'controllers');
define('PATH_APP_MODELS', PATH_APP_SRC . DS . 'models');
define('PATH_APP_VIEWS', PATH_APP_SRC . DS . 'views');
define('PATH_APP_VIEWS_SHARED', PATH_APP_VIEWS . DS . 'shared');
define('PATH_APP_LIBS', PATH_APP_SRC . DS . 'libs');