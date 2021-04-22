<?php

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', realpath('.'));
define('FRAMEWORK_ROOT', ROOT . DS . 'framework');

require_once(FRAMEWORK_ROOT . DS . 'constants.php');
require_once(FRAMEWORK_ROOT . DS . 'bootstrap.php');