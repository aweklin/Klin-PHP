<?php

namespace Framework\Interfaces;

use Framework\Core\RequestHandlerResponse;

/**
 * Defines a contract for handling request from a controller.
 */
interface IRequestHandler {

    /**
     * Executes request.
     * 
     * @param IRequest $request The request being executed.
     */
    function invoke(IRequest $request) : RequestHandlerResponse;

}