<?php

namespace App\Src\Controllers;

use Framework\Core\{Controller, Response};
use Framework\Infrastructure\Security;
use App\Src\Models\User;

class AccountController extends Controller {

    public function login() {
        if (User::isLoggedIn()) {
            Response::redirect(DEFAULT_REDIRECT_AFTER_LOGIN);
            exit;
        }

        if ($this->request->isPost(['username', 'password'])) {
            if ($this->request->hasMissingItems()) {
                $this->response->json(true, $this->request->getMissingItems());
                return;
            }
            // get input
            $username = $this->request->get('username');
            $password = $this->request->get('password');

            // authenticate user account
            $user = new User();
            $userInfo = $user->set($this->request->getPostedData())->findByUsername();
            if ($user->hasError()) {
                $this->response->json(true, $user->getErrorMessage());
                return;
            }
            $rememberMe = $this->request->get('remember_me');                    
            $rememberMe = ($rememberMe ? true : false);
            
            if ($userInfo && $user->login($this->request->get('password'), $rememberMe)) {
                if ($userInfo->hasError()) {
                    $this->response->json(true, $userInfo->getErrorMessage());
                    return;
                }

                $this->response->json(false, 'Login successful');
                return;                
            }
                
            $this->response->json(true, 'Incorrect username or password.');            
        } else {
            $this->response->view('account.login');
        }
    }

    public function logout() {
        if (User::getCurrentUser()) {
            User::getCurrentUser()->logout();
        }
        Response::redirect();
    }

    public function register() {
        if ($this->request->isPost()) {
            // create user account
            $user = new User();
            $user->set($this->request->getPostedData())->register($this->request->get('confirm_password'));
            if ($user->hasError()) {
                $this->response->json(true, $user->getErrorMessage());
            } else {
                $userInfo = $user->findByUsername($this->request->get('username'));
                $user->login();
                if ($user->hasError()) {
                    $this->response->json(true, $user->getErrorMessage());
                } else {
                    $this->response->json(false, 'Profile creation was successful.');
                }
            }
        } else {
            $this->response->view('account/register');
        }
    }

}