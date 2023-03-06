<?php

namespace Framework\Core;

use Exception;
use Framework\Core\Request;
use Framework\Interfaces\IRouter;

/**
 * Contains some low lever methods that the app uses and some security mechanism.
 * It is also responsible for managing app routes.
 */
class App {

    /**
     * Initializes a new instance of the App with the request url parameter.
     */
    public function __construct(IRouter $router, array $requestUrl) {
        $this->_setErrorReporting();
        $this->_unregisterGlobals();
        $router->route($requestUrl);
    }

    /**
     * Checks if there is access to the internet
     * 
     * @return bool
     */
    public static function hasInternetAccess() : bool {
        try {
            $isConnected = @fsockopen("www.bing.com", 80);
            if (!$isConnected) return false;
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Returns the user IP address.
     * 
     * @return string
     */
	public static function getIP() : string {
		// credit: http://stackoverflow.com/questions/15699101/get-the-client-ip-address-using-php
	    $ipaddress = '';
	    if (isset($_SERVER['HTTP_CLIENT_IP']))
	        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED']))
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
	        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_FORWARDED']))
	        $ipaddress = $_SERVER['HTTP_FORWARDED'];
	    else if(isset($_SERVER['REMOTE_ADDR']))
	        $ipaddress = $_SERVER['REMOTE_ADDR'];
	    else
	        $ipaddress = 'UNKNOWN';
	    return $ipaddress;
	}
	
    /**
     * Report errors only during development
     */
    private function _setErrorReporting() {
        error_reporting(E_ALL);
        if (IS_DEVELOPMENT) {
            ini_set('display_errors', 1);
        } else {
            ini_set('display_errors', 0);
            ini_set('log_errors', 1);
            if (!file_exists(PATH_APP_TMP_LOGS . DS . 'errors.log')) {
                mkdir(PATH_APP_TMP_LOGS, 0777, true);
            }
            ini_set('error_log', PATH_APP_TMP_LOGS . DS . 'errors.log');
        }
    }

    /**
     * Unregister globals
     */
    private function _unregisterGlobals() {
        if (ini_get('register_globals')) {
            $globalArrays = ['_SERVER', '_SESSION', '_COOKIE', '_ENV', '_POST', '_GET', '_FILES', '_REQUEST'];
            foreach($globalArrays as $item) {
                foreach($GLOBALS[$item] as $key => $value) {
                    if ($GLOBALS[$key] === $value) {
                        unset($GLOBALS[$key]);
                    }
                }
            }
        }
    }

    /**
     * This helper function displays data parsed to it and immediately terminates the app from continuing to execute.
     * It is meant for debugging.
     * 
     * @param mixed $data The data to be dumped. It accepts anything
     */
    public static function end($data = '') {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
        die();
    }
}