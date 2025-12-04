<?php

namespace Framework\Core;

use Framework\Interfaces\IJson;
use Framework\Interfaces\ILogger;
use Framework\Utils\Str;

class Json implements IJson {

    public const STATUS_CODE_OK = 200;
    public const STATUS_CODE_CREATED = 201;
    public const STATUS_CODE_ACCEPTED = 202;
    public const STATUS_CODE_NO_CONTENT = 204;
    public const STATUS_CODE_BAD_REQUEST = 400;
    public const STATUS_CODE_UNAUTHORIZED = 401;
    public const STATUS_CODE_NOT_FOUND = 404;
    public const STATUS_CODE_METHOD_NOT_ALLOWED = 405;
    public const STATUS_CODE_CONFLICT = 409;
    public const STATUS_CODE_UNSUPPORTED_MEDIA_TYPE = 415;
    public const STATUS_CODE_UNPROCESSED_ENTITY = 422;
    public const STATUS_CODE_TOO_MANY_REQUEST = 429;
    public const STATUS_CODE_INTERNAL_SERVER_ERROR = 500;

    private ILogger $_logger;
    private array $_statusCodeMap = [];
    private const HTTP_VERSION = 'HTTP/1.1 ';

    public function __construct(ILogger $logger) {
        $this->_logger = $logger;

        $this->_statusCodeMap = [
            self::STATUS_CODE_OK => self::HTTP_VERSION . self::STATUS_CODE_OK . ' OK',
            self::STATUS_CODE_CREATED => self::HTTP_VERSION . self::STATUS_CODE_CREATED . ' Created',
            self::STATUS_CODE_ACCEPTED => self::HTTP_VERSION . self::STATUS_CODE_ACCEPTED . ' Accepted',
            self::STATUS_CODE_NO_CONTENT => self::HTTP_VERSION . self::STATUS_CODE_NO_CONTENT . ' No Content',
            self::STATUS_CODE_BAD_REQUEST => self::HTTP_VERSION . self::STATUS_CODE_BAD_REQUEST . ' Bad Request',
            self::STATUS_CODE_UNAUTHORIZED => self::HTTP_VERSION . self::STATUS_CODE_UNAUTHORIZED . ' Unauthorized',
            self::STATUS_CODE_NOT_FOUND => self::HTTP_VERSION . self::STATUS_CODE_NOT_FOUND . ' Not Found',
            self::STATUS_CODE_METHOD_NOT_ALLOWED => self::HTTP_VERSION . self::STATUS_CODE_METHOD_NOT_ALLOWED . ' Method Not Allowed',
            self::STATUS_CODE_CONFLICT => self::HTTP_VERSION . self::STATUS_CODE_CONFLICT . ' Conflict',
            self::STATUS_CODE_UNSUPPORTED_MEDIA_TYPE => self::HTTP_VERSION . self::STATUS_CODE_UNSUPPORTED_MEDIA_TYPE . ' Unsupported Media Type',
            self::STATUS_CODE_UNPROCESSED_ENTITY => self::HTTP_VERSION . self::STATUS_CODE_UNPROCESSED_ENTITY . ' Unprocessable Entity',
            self::STATUS_CODE_TOO_MANY_REQUEST => self::HTTP_VERSION . self::STATUS_CODE_TOO_MANY_REQUEST . ' Too Many Requests',
            self::STATUS_CODE_INTERNAL_SERVER_ERROR => self::HTTP_VERSION . self::STATUS_CODE_INTERNAL_SERVER_ERROR . ' Internal Server Error',
        ];
    }

    /**
     * Standard response for successful HTTP requests. The actual response will depend on the request method used. 
     * In a GET request, the response will contain an entity corresponding to the requested resource. 
     * In a POST request, the response will contain an entity describing or containing the result of the action.
     */
    function ok(bool $hasError = false, string $message = '', array|null $data = null, int $statusCode = 200) : void {
        $this->completed($hasError, $message, $data, $statusCode);
    }
    
    /**
     * The request has been fulfilled.
     * 
     * Returns 200 status code.
     */
    function success(string $message = 'Operation succeeded.', array|null $data = null) : void {
        $this->completed(false, $message, $data, statusCode: self::STATUS_CODE_OK);
    }
    
    /**
     * The request has been fulfilled, resulting in the creation of a new resource.
     * 
     * Returns 201 status code
     */
    function created(string $message = 'Resource created successfully', array|null $data = null) : void {
        $this->completed(false, $message, $data, statusCode: self::STATUS_CODE_CREATED);
    }
    
    /**
     * The request has been accepted for processing, but the processing has not been completed. 
     * The request might or might not be eventually acted upon, and may be disallowed when processing occurs.
     * 
     * Returns 202 status code
     */
    function accepted(string $message = 'Resource accepted successfully', array|null $data = null) : void {
        $this->completed(false, $message, $data, statusCode: self::STATUS_CODE_ACCEPTED);
    }
    
    
    /**
     * The server successfully processed the request, and is not returning any content.
     * 
     * Returns 204 status code
     */
    function noContent(string $message = 'Resource accepted successfully', array|null $data = null) : void {
        $this->completed(false, $message, $data, statusCode: self::STATUS_CODE_NO_CONTENT);
    }

     /**
     * The server cannot or will not process the request due to an apparent client error (e.g., malformed request syntax, size too large, invalid request message framing, or deceptive request routing).
     * 
     * Returns 400 status code
     */
    function badRequest(string $message = 'Bad request.', array|null $data = null) : void {
        $this->completed(true, $message, $data, statusCode: self::STATUS_CODE_BAD_REQUEST);
    }
    
     /**
     * The server cannot or will not process the request due to an apparent client error (e.g., malformed request syntax, size too large, invalid request message framing, or deceptive request routing).
     * 
     * Returns 401 status code
     */
    function unauthorized(string $message = 'Authorization failed.') : void {
        $this->completed(true, $message, statusCode: self::STATUS_CODE_UNAUTHORIZED);
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
        $this->completed(true, $message, statusCode: self::STATUS_CODE_UNAUTHORIZED);
    }

    /**
     * The requested resource could not be found but may be available in the future. Subsequent requests by the client are permissible.
     * 
     * Returns 404 status code
     */
    function notFound(string $message = 'Request not found.') : void {
        $this->completed(true, $message, statusCode: self::STATUS_CODE_NOT_FOUND);
    }
    
    /**
     * A request method is not supported for the requested resource; for example, a GET request on a form that requires data to be presented via POST, or a PUT request on a read-only resource.
     * 
     * Returns 405 status code
     */
    function methodNotAllowed(string $supportedRequestType) : void {
        $message = Str::toUpper($_SERVER['REQUEST_METHOD']) . " method not allowed. Only a {$supportedRequestType} is supported.";
        $this->completed(true, $message, statusCode: self::STATUS_CODE_METHOD_NOT_ALLOWED);
    }

    /**
     * A request method is not supported for the requested resource; for example, a GET request on a form that requires data to be presented via POST, or a PUT request on a read-only resource.
     * 
     * Returns 415 status code
     */
    function unsupported(string $message = 'Unsupported content type.') : void {
        $this->completed(true, $message, statusCode: self::STATUS_CODE_UNSUPPORTED_MEDIA_TYPE);
    }

    /**
     * The user has sent too many requests in a given amount of time. Intended for use with rate-limiting schemes.
     * 
     * Returns 429 status code
     */
    function tooManyRequests(string $message = 'Request not found.') : void {
        $this->completed(true, $message, statusCode: self::STATUS_CODE_TOO_MANY_REQUEST);
    }
    
    /**
     * A generic error message, given when an unexpected condition was encountered and no more specific message is suitable.
     * 
     * Returns 500 status code.
     */
    function error(string $message = 'An internal server error occurred.', array|null $data = null) : void {
        $this->completed(true, $message, $data, statusCode: self::STATUS_CODE_INTERNAL_SERVER_ERROR);
    }

    /**
     * Standard response for successful HTTP requests. The actual response will depend on the request method used. 
     * In a GET request, the response will contain an entity corresponding to the requested resource. 
     * In a POST request, the response will contain an entity describing or containing the result of the action.
     */
    function completed(bool $hasError = false, string $message = '', array|null $data = null, int $statusCode = 200) : void {
        //http_response_code($statusCode);
        header($this->_statusCodeMap[$statusCode], response_code: $statusCode);

        try {
            header('Content-Type: application/json');
        } catch (\Exception $e) {
            $this->_logger->error('Error setting Content-Type: application/json: ' . $e->getMessage());
        }
        echo json_encode(['hasError' => $hasError, 'message' => $message, 'data' => $data]);
        exit;
    }
}