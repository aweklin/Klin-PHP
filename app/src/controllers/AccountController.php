<?php

namespace App\Src\Controllers;

use Framework\Core\{Controller, Response};
use Framework\Infrastructure\Security;
use App\Src\Models\User;

class AccountController extends Controller {

    public function login() {
        if (User::isLoggedIn()) {
            Response::redirect('');
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
            if ($userInfo && Security::isVerified($this->request->get('password'), $userInfo->password)) {
                $rememberMe = $this->request->get('remember_me');                    
                $rememberMe = ($rememberMe ? true : false);
                
                $user->login($rememberMe);
                if ($user->hasError()) {
                    $this->response->json(true, $user->getErrorMessage());
                } else {
                    $this->response->json(false, 'Login successful');
                }
            } else {
                $this->response->json(true, 'Incorrect username or password.');
            }
        } else {
            $this->response->view('account/login');
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