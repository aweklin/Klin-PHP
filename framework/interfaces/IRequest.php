<?php

namespace Framework\Interfaces;

interface IRequest {
    /** Returns the request method */
    function getMethod() : string;

    /**
     * Returns the validation errors
     */
    function getValidationErrors() : string;

    /**
     * Specifies if html tags should be stripped from validation errors
     */
    function stripHtmlFromValidationErrors(bool $removeHtmlFromValidationErrors) : void;

    /**
     * Checks if there is any validation error, based on the expected fields and validation rules supplied.
     */
    function hasValidationErrors() : bool;

    /**
     * Checks if the request method is post. 
     * 
     * @param array $expectedItem Optional. If the expectedItems is passed, it verifies each post item if it contains all the expected keys.
     * 
     * @return bool
     */
    function isPost(array $expectedItems = [], bool $validateFormToken = true) : bool;

    /**
     * Checks if the request method is get.
     * 
     * @return bool
     */
    function isGet() : bool;

    /**
     * Checks if the request method is put.
     * 
     * @param array $expectedItem Optional. If the expectedItems is passed, it verifies each post item if it contains all the expected keys.
     * 
     * @return bool
     */
    function isPut(array $expectedItems = [], bool $validateFormToken = true) : bool;

    /**
     * Checks if the request method is delete.
     * 
     * @return bool
     */
    function isDelete(bool $validateFormToken = true) : bool;

    /**
     * Returns the data from post request, with the option of retuning object or array
     * 
     * @param array $expectedItem Optional. If the expectedItems is passed, it verifies each post item if it contains all the expected keys.
     * @param bool $returnDataAsArray True by default. Determines if the output should be returned as array or object.
     * 
     * @return mixed
     */
    function getPostedData(array $expectedItem = [], bool $returnDataAsArray = true);

    /**
     * Returns a value, indicating wether the post/put request has some missing key(s).
     */
    function hasMissingItems() : bool;

    /**
     * Returns a value, indicating wether the post/put request has some missing key(s).
     */
    function isValid(array $expectedItems, bool $validateFormToken = true) : bool;

    /**
     * Returns all the missing items from the post/put request as string.
     */
    function getMissingItems() : string;

    /**
     * Returns the value of the specified key from get/post/put request.
     * 
     * @param string $key Specifies the key to get its value.
     * @param bool $sanitizeInput True by default. Indicates wether the input value is returned sanitized.
     * 
     * @return mixed
     */
    function get(string $key, bool $sanitizeInput = true, bool $isHeaderKey = false) : mixed;

    /**
     * Returns the authorization value for this request.
     * 
     * @return string|null
     */
    function getAuthorizationHeaderValue() : ?string;

    /**
     * Returns the content type for this request.
     * 
     * @return string|null
     */
    function getContentTypeHeaderValue() : ?string;

    /**
     * Returns the current page uri
     * 
     * @return string
     */
    static function getCurrentPage() : string;

    /**
     * Makes a RESTFUL api call.
     * 
     * @param string $type One of POST, PUT, GET, DELETE
     * @param string $url Specifies the endpoint
     * @param array $parameters Specifies the request parameters
     * @param array $headers Specifies the request header.
     * 
     * @return array
     */
    function webService(string $type, string $url, array $parameters = [], array $headers = []) : array;

    /**
     * Checks if a file was uploaded with the given key.
     * 
     * @param string $key Specifies the file key.
     * 
     * @return bool
     */
    function hasFile(string $key) : bool;

    /**
     * Checks if the form token is present in the request.
     * 
     * @return bool
     */
    function hasFormToken() : bool;

    /**
     * Validates the form token in the request.
     * 
     * @return bool
     */
    function isFormTokenValid() : bool;

    /**
     * Returns the uploaded file for the given key.
     * 
     * @param string $key Specifies the file key.
     * 
     * @return mixed
     */
    function getFile(string $key) : mixed;
}