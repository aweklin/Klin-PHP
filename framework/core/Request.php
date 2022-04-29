<?php

namespace Framework\Core;

use Framework\Utils\{Str, Ary};

/**
 * Encapsulates various methods to interact with the request data.
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
final class Request {

    private $_requestObject = null;
    private $_data = [];
    private $_missingItems = [];

    public function __construct() {
        $this->_setRequestData();
    }

    /**
     * Checks if the request method is post. 
     * 
     * @param array $expectedItem Optional. If the expectedItems is passed, it verifies each post item if it contains all the expected keys.
     * 
     * @return bool
     */
    public function isPost(array $expectedItems = []) : bool {
        if ($expectedItems) {
            $this->_checkForMissingItemsInRequest($this->_requestObject, $expectedItems);
        }
        return $this->_getRequestMethod() === 'post';
    }

    /**
     * Checks if the request method is get.
     * 
     * @return bool
     */
    public function isGet() : bool {
        return $this->_getRequestMethod() === 'get';
    }


    /**
     * Checks if the request method is put.
     * 
     * @param array $expectedItem Optional. If the expectedItems is passed, it verifies each post item if it contains all the expected keys.
     * 
     * @return bool
     */
    public function isPut(array $expectedItems = []) : bool {
        if ($expectedItems) {
            $this->_checkForMissingItemsInRequest($this->_requestObject, $expectedItems);
        }
        return $this->_getRequestMethod() === 'put';
    }


    /**
     * Checks if the request method is delete.
     * 
     * @return bool
     */
    public function isDelete() : bool {
        return $this->_getRequestMethod() === 'delete';
    }

    /**
     * Returns the data from post request, with the option of retuning object or array
     * 
     * @param array $expectedItem Optional. If the expectedItems is passed, it verifies each post item if it contains all the expected keys.
     * @param bool $returnDataAsArray True by default. Determines if the output should be returned as array or object.
     * 
     * @return mixed
     */
    public function getPostedData(array $expectedItem = [], bool $returnDataAsArray = true) {
        if ($expectedItem)
            $this->_checkForMissingItemsInRequest($this->_requestObject, $expectedItem);

        if (!$returnDataAsArray) 
            return $this->_requestObject;
        else
            return $this->_data;
    }

    /**
     * Returns a value, indicating wether the post/put request has some missing key(s).
     */
    public function hasMissingItems() : bool {
        return count($this->_missingItems) > 0;
    }

    /**
     * Returns all the missing items from the post/put request as string.
     */
    public function getMissingItems() : string {
        if (!$this->hasMissingItems()) return '';

        return join(', ', $this->_missingItems) . ' key ' . (count($this->_missingItems) == 1 ? 'is ' : 's are ') . ' missing in your request body.';
    }

    /**
     * Returns the value of the specified key from get/post/put request.
     * 
     * @param string $key Specifies the key to get its value.
     * @param bool $sanitizeInput True by default. Indicates wether the input value is returned sanitized.
     * 
     * @return mixed
     */
    public function get(string $key, bool $sanitizeInput = true) {
        if (!$this->_data) return null;
        if (!isset($this->_data[$key])) return null;

        if (is_object($this->_data[$key]) || is_array($this->_data[$key])) {
            $output = [];
            $data = Ary::convertFromObject($this->_data[$key]);
            $i = 0;
            foreach($data as $item) {
                if (is_array($item)) {
                    if (Ary::isAssociative($item)) {
                        $keys = array_keys($item);
                        foreach($keys as $key) {
                            $output[$i][$key] = $this->_sanitize($item[$key]);
                        }
                    } else {
                        $output[$i] = $this->_sanitize($item);
                    }
                } else {
                    $output[$i] = $this->_sanitize($item);
                }
                $i++;
            }
            return $output;
        }
        return (!$sanitizeInput ? $this->_data[$key] : $this->_sanitize($this->_data[$key]));
    }

    /**
     * Returns the current request method.
     * 
     * @return string
     */
    private function _getRequestMethod() : string {
        return Str::toLower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Sets the request data from POST/PUT for later use.
     */
    private function _setRequestData() {
        $this->_requestObject = null;
        $this->_data = [];

        $postData = null;
        if ($this->isPost()) {
            $postData = $_POST; // this is probably coming from a form
        }
        if ($this->isPut()) {
            parse_str(file_get_contents('php://input'), $_PUT);
            foreach($_PUT as $item) {
                $postData = \json_decode($item, true);
            }
        }
        if (!$postData) {   // this may be coming from api client like PostMan
            $postData = file_get_contents("php://input");
            if ($postData) {
                $postData = json_decode($postData);
            }
        }

        // set the request object
        $this->_requestObject = $postData;

        if ($this->_requestObject) {            
            // set the request data [array]
            $this->_data = [];
            $this->_convertRequestObjectToArray($this->_requestObject, $this->_data);
        }
    }
    
    /**
     * Sanitizes the given value and returns a safe to use string.
     * 
     * @param mixed $value Specifies the value being sanitized.
     * 
     * @return string
     */
    private function _sanitize($value) : string {
        return htmlentities($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Checks if there are missing items from the request object.
     * 
     * @param mixed $request The request object being checked.
     * @param array $expectedItems List of the expected array keys from the request object
     */
    private function _checkForMissingItemsInRequest($request, array $expectedItems) {
        $this->_missingItems = [];

        if ($request && $expectedItems) {        
            foreach($expectedItems as $item) {
                if (\is_array($item) && !isset($request[$item])) {
                    array_push($this->_missingItems, $item);
                } else if (\is_object($item) && !isset($request->$item)) {
                    array_push($this->_missingItems, $item);
                }
            }
        }
    }

    /**
     * Converts an object request to array.
     * 
     * @param mixed $requestObject The request object to convert to array
     * @param array Passed by ref. The output value.
     */
    private function _convertRequestObjectToArray($requestObject, array &$output){
        if ($requestObject) {
            foreach($requestObject as $key => $value) {
                if (is_object($value)) {
                    return $this->_convertRequestObjectToArray($value, $output);
                } else {
                    $output[$key] = $value;
                }
            }
        }
    }

    /**
     * Takes the user to the requested page. If the controller/action was not found, ErrorController::notFound method is processeds.
     * 
     * @param array $url The request url.
     */
    public static function route(array $url) {
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

            // get the action
            $action     = (isset($url[0]) && $url[0] ? ucwords($url[0]) : DEFAULT_ACTION);
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
        $controllerClass= new $controller($controllerName, $action);
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

    /**
     * Returns the current page uri
     * 
     * @return string
     */
    public static function getCurrentPage() : string {
        $currentPage = mb_strtolower($_SERVER['REQUEST_URI']);
        if ($currentPage == mb_strtolower(APP_BASE_URL) || $currentPage == mb_strtolower(APP_BASE_URL . DEFAULT_CONTROLLER . '/' . DEFAULT_ACTION)) {
            $currentPage = mb_strtolower(APP_BASE_URL . DEFAULT_CONTROLLER);
        }
    
        return $currentPage;
    }

    public function webService(string $type, string $url, array $parameters = [], array $headers = []) : array {
        $type = Str::toLower($type);

        // some validations
        $acceptableRequestTypes = ['get', 'post', 'put', 'delete'];
        if (!in_array($type, $acceptableRequestTypes)) {
            throw new Exception('Request type must be one of: ' . join(', ', $acceptableRequestTypes));            
        }
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new Exception('Invalid url: ' . $url);
        }
        if (in_array($type, [$acceptableRequestTypes[1], $acceptableRequestTypes[2]]) && !$parameters) {
            throw new Exception('Parameter is expected for your ' . $type . ' request.');
        }

        // prepare request
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $url);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);

        // set header
        $jsonContentTypeKey = 'Content-Type';
        $jsonContentTypeValue = 'application/json';

        $jsonContentType = $jsonContentTypeKey.$jsonContentTypeValue;
        if (!$headers) {
            $headers  = [];
            array_push($headers, $jsonContentType);
        } else {
            $hasContentType = false;
            if (Ary::isAssociative($headers)) {
                $jsonContentType = [$jsonContentTypeKey => $jsonContentTypeValue];
                foreach($headers as $key => $value) {
                    if (Str::contains($jsonContentTypeKey, Str::removeSpaces($key)) || Str::contains($jsonContentTypeValue, Str::removeSpaces($value))) {
                        $hasContentType = true;
                        break;
                    }
                }
            } else {
                foreach($headers as $header) {
                    if ($jsonContentType == Str::removeSpaces($header)) {
                        $hasContentType = true;
                        break;
                    }
                }
            }

            if (!$hasContentType) {
                $headers[$jsonContentTypeKey] = $jsonContentTypeValue;
            }
        }
        if ($headers) {
            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
        }
        if ($parameters) {
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, json_encode($parameters));
        }

        $curlExecution = curl_exec($curlHandle);
        if ($curlExecution === false) {
            throw new Exception('Request error: ' . curl_error($curlHandle));
        } else {
            $result = json_decode($curlExecution, true);
            curl_close($curlHandle);

            return $result;
        }
    }

}