<?php

namespace Framework\Core;

/**
 * Represents the response received from a request handler.
 */
class RequestHandlerResponse {

    /**
     * Creates a new instance of the Framework/Core/RequestHandlerResponse class.
     * 
     * @param bool $hasError Indicates wether the request was successfully executed or not.
     * @param string $message Specifies the message being returned to the user.
     * @param array $data Optional - Specifies the data being returned.
     */
    public function __construct(public bool $hasError, public string $message, public array $data = []) {}
}