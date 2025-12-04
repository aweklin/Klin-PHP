<?php

namespace Framework\Interfaces;

use Framework\Core\RequestHandlerResponse;
use Framework\Enums\RequestType;

interface IHttpClient {

    static function getInstance() : IHttpClient;
    function setRequestType(RequestType $requestType) : IHttpClient;
    function setUrl(string $url) : IHttpClient;
    function setHeaders(array $headers) : IHttpClient;
    function setPayload(array $payload) : IHttpClient;

    /**
     * Makes a RESTFUL api call.
     */
    function execute() : RequestHandlerResponse;

}