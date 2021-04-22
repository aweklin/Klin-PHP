<?php

namespace Framework\Core;

use Framework\Core\{Request, Response};
use Framework\Infrastructure\Session;
use Framework\Utils\Str;
use App\Src\Models\User;

/**
 * This is the base controller class which all controllers in the app extend.
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
class Controller {

    protected $_controller;
    protected $_action;
    protected Request $request;
    
    public Response $response;

    /**
     * Creates a new instance of the Controller class with the name and action passed.
     * 
     * @param string $controller The controller name being instantiated.
     * @param string $action The action name being invoked.
     */
    public function __construct(string $controller, string $action) {
        $this->_controller  = $controller;
        $this->_action      = $action;
        $this->request      = new Request();
        $this->response     = new Response(Str::toLower(str_replace(CONTROLLER_SUFFIX, '', $controller)), $action);

        Session::set(APP_MESSAGE, null);

        if (isset($this->confirmAuthorization)) {
			$this->_validateAuthentication();
		}
    }

    /**
     * Checks if the user is logged in to the app, otherwise, he's taking to login page for proper login.
     * 
     * @return void
     */
    private function _validateAuthentication() {
        if (!User::isLoggedIn()) {
            Response::redirect('account/login');
        }
    }

}