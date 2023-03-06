<?php

namespace Framework\Core;

use Framework\Interfaces\IDependencyContainer;
use Framework\Interfaces\IRouter;
use Framework\Utils\Str;

class Router implements IRouter {

    private IDependencyContainer $_dependencyContainer;

    public function __construct(IDependencyContainer $dependencyContainer) {
        $this->_dependencyContainer = $dependencyContainer;
    }
    
    public function route(array $url) : void {
        $controller     = '';
        $controllerName = '';
        $action         = '';
        $parameters     = [];

        // get the controller, action and parameter from url
        if (!$url) {
            
            $controller = DEFAULT_CONTROLLER . CONTROLLER_SUFFIX;
            $action     = DEFAULT_ACTION;

        } else {

            // get the controller
            $controller = (isset($url[0]) && $url[0] ? ucwords($url[0]) : DEFAULT_CONTROLLER) . CONTROLLER_SUFFIX;
            array_shift($url);
            if (Str::contains($controller, '-')) {
                $controllerArray = explode('-', $controller);
                $controller = join('', array_map(function($item) {
                    return ucfirst($item);
                }, $controllerArray));
            }

            // get the action
            $action     = (isset($url[0]) && $url[0] ? ucwords($url[0]) : DEFAULT_ACTION);
            if (Str::contains($action, '')) {
                $action = join('', explode('-', $action));
            }
            array_shift($url);

            // parameter
            $parameters = $url;

        }

        $controllerName = $controller;

        $tempController = $controller;

        $controllerPath = 'App\Src\Controllers\\';

        if (!file_exists(PATH_APP_CONTROLLERS . DS . $controllerName . '.php')) {
            $controller = $controllerPath . 'Error' . CONTROLLER_SUFFIX;
            $action     = 'notFound';
            $controllerName = $controller;
        }

        $controller = (!Str::contains($controller, $controllerPath) ? $controllerPath : '') . $controller;
        $controllerClass = $this->_dependencyContainer->get($controller);
        if (!file_exists(PATH_APP_CONTROLLERS . DS . $tempController . '.php')) {
            $controllerClass->response->setTitle('Not Found!');
        }

        if (!method_exists($controller, $action)) {
            $controller = $controllerPath .  'Error' . CONTROLLER_SUFFIX;
            $action     = 'notFound';
            $controllerName = $controller;

            $controllerClass= new $controller($controllerName, $action);

            $controllerClass->response->setTitle('Not Found!');
        }
        call_user_func_array([$controllerClass, $action], $parameters);
    }

}