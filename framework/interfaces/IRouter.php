<?php

namespace Framework\Interfaces;

/**
 * Defines a router object for managing routes.
 */
interface IRouter {

    /**
     * Evaluates the requested uri and proceeds to instantiate the controller and call the action method specified. 
     * If the controller/action was not found, ErrorController::notFound method is processed.
     * 
     * @param array $url The request url.
     */
    function route(array $url) : void;
}