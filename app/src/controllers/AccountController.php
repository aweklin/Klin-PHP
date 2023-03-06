<?php

namespace App\Src\Controllers;

use App\Src\Controllers\Handlers\LoginRequestHandler;
use Framework\Core\{Controller, Response};
use App\Src\Models\User;

class AccountController extends Controller {

    public function __construct(
        private LoginRequestHandler $_loginRequestHandler, 
        private User $_user, 
        string $controller, 
        string $action) {
        parent::__construct($controller, $action);
    }

    public function login() {
        if (User::isLoggedIn()) {
            Response::redirect(DEFAULT_REDIRECT_AFTER_LOGIN);
            exit;
        }
        
        if ($this->request->isPost()) {
            $response = $this->_loginRequestHandler->invoke($this->request);
            return $this->response->json($response->hasError, $response->message);
        }

        $this->response->setTitle('Welcome to ' . SITE_TITLE);
        $this->response->view('account.login');        
    }

    public function logout() {
        if (User::getCurrentUser()) {
            User::getCurrentUser()->logout();
        }
        Response::redirect();
    }

    public function register() {
        if (!$this->request->isPost())
            return $this->response->view('account/register');

        // create user account
        $this->_user
            ->set($this->request->getPostedData())
            ->register($this->request->get('confirm_password'));
        if ($this->_user->hasError()) {
            $this->response->json(true, $this->_user->getErrorMessage());
        } else {
            $this->_user->login($this->request->get('password'));
            if ($this->_user->hasError()) {
                $this->response->json(true, $this->_user->getErrorMessage());
            } else {
                $this->response->json(false, 'Profile creation was successful.');
            }
        }
    }

}