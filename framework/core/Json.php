<?php

namespace Framework\Core;

use Framework\Interfaces\IJson;
use Framework\Interfaces\ILogger;
use Framework\Utils\Str;

class Json implements IJson {

    private ILogger $_logger;

    public function __construct(ILogger $logger) {
        $this->_logger = $logger;
    }

    /**
     * Standard response for successful HTTP requests. The actual response will depend on the request method used. 
     * In a GET request, the response will contain an entity corresponding to the requested resource. 
     * In a POST request, the response will contain an entity describing or containing the result of the action.
     */
    function ok(bool $hasError = false, string $message = '', array|null $data = null, int $statusCode = 200) : void {
        http_response_code($statusCode);
        try {
            header('Content-Type: application/json');
        } catch (\Exception $e) {
            $this->_logger->log('Error setting Content-Type: application/json: ' . $e->getMessage());
        }
        echo json_encode(['hasError' => $hasError, 'message' => $message, 'data' => $data]);
    }
    
    /**
     * The request has been fulfilled.
     * 
     * Returns 200 status code.
     */
    function success(string $message = 'Operation succeeded.', array|null $data = null) : void {
        $this->ok(false, $message);
    }
    
    /**
     * The request has been fulfilled, resulting in the creation of a new resource.
     * 
     * Returns 201 status code
     */
    function created(string $message = 'Resource created successfully') : void {
        $this->ok(false, $message, statusCode: 201);
    }
    
    /**
     * The request has been accepted for processing, but the processing has not been completed. 
     * The request might or might not be eventually acted upon, and may be disallowed when processing occurs.
     * 
     * Returns 202 status code
     */
    function accepted(string $message = 'Resource accepted successfully') : void {
        $this->ok(false, $message, statusCode: 202);
    }
    
    
    /**
     * The server successfully processed the request, and is not returning any content.
     * 
     * Returns 204 status code
     */
    function noContent(string $message = 'Resource accepted successfully') : void {
        $this->ok(false, $message, statusCode: 204);
    }

     /**
     * The server cannot or will not process the request due to an apparent client error (e.g., malformed request syntax, size too large, invalid request message framing, or deceptive request routing).
     * 
     * Returns 400 status code
     */
    function badRequest(string $message = 'Bad request.') : void {
        $this->ok(true, $message, statusCode: 400);
    }
    
     /**
     * The server cannot or will not process the request due to an apparent client error (e.g., malformed request syntax, size too large, invalid request message framing, or deceptive request routing).
     * 
     * Returns 401 status code
     */
    function unauthorized(string $message = 'Authorization failed.') : void {
        $this->ok(true, $message, statusCode: 401);
    }
    
    /**
     * Similar to 403 Forbidden, but specifically for use when authentication is required and has failed or has not yet been provided. 
     * The response must include a WWW-Authenticate header field containing a challenge applicable to the requested resource. 
     * See Basic access authentication and Digest access authentication. 401 semantically means "unauthorised", the user does not have valid authentication credentials for the target resource.
     * Note: Some sites incorrectly issue HTTP 401 when an IP address is banned from the website (usually the website domain) and that specific address is refused permission to access a website.
     * 
     * Returns 403 status code
     */
    function forbidden(string $message = 'Access to that resource is forbidden.') : void {
        $this->ok(true, $message, statusCode: 403);
    }

    /**
     * The requested resource could not be found but may be available in the future. Subsequent requests by the client are permissible.
     * 
     * Returns 404 status code
     */
    function notFound(string $message = 'Request not found.') : void {
        $this->ok(true, $message, statusCode: 404);
    }
    
    /**
     * A request method is not supported for the requested resource; for example, a GET request on a form that requires data to be presented via POST, or a PUT request on a read-only resource.
     * 
     * Returns 405 status code
     */
    function methodNotAllowed(string $supportedRequestType) : void {
        $message = Str::toUpper($_SERVER['REQUEST_METHOD']) . " method not allowed. Only a {$supportedRequestType} is supported.";
        $this->ok(true, $message, statusCode: 405);
    }

    /**
     * A request method is not supported for the requested resource; for example, a GET request on a form that requires data to be presented via POST, or a PUT request on a read-only resource.
     * 
     * Returns 415 status code
     */
    function unsupported(string $message = 'Unsupported content type.') : void {
        $this->ok(true, $message, statusCode: 415);
    }

    /**
     * The user has sent too many requests in a given amount of time. Intended for use with rate-limiting schemes.
     * 
     * Returns 429 status code
     */
    function tooManyRequests(string $message = 'Request not found.') : void {
        $this->ok(true, $message, statusCode: 429);
    }
    
    /**
     * A generic error message, given when an unexpected condition was encountered and no more specific message is suitable.
     * 
     * Returns 500 status code.
     */
    function error(string $message = 'An internal server error occurred.') : void {
        $this->ok(true, $message, statusCode: 500);
    }
}