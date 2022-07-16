<?php

namespace App\Src\Models;

use Framework\Core\{Database, Model};
use Framework\Infrastructure\{BcryptPasswordHasher, Cookie, Session, Security};
use Framework\Core\Validators\{Validator, ValidationRule};
use Framework\Core\Validators\Rules\{RequiredRule, MinimumLengthRule, MaximumLengthRule, UniqueRule, EmailRule, EqualRule};
use App\Src\Models\UserSession;
use Framework\Decorator\PasswordEncryptor;
use Framework\Interfaces\IPasswordVerifier;

class User extends Model {

    var $hiddenFields = ['password'];

    private static $_currentLoggedInUser = null;
    private static $_isLoggedIn = false;

    private PasswordEncryptor $_passwordEncryptor;

    public function __construct($idOrUsername = '') {
        parent::__construct();

        self::$_isLoggedIn = false;

        // choose password has algorithm
        $this->_passwordEncryptor = new BcryptPasswordHasher(12);

        if ($idOrUsername) {
            $userInfo = null;
            if (is_numeric($idOrUsername)) {
                $userInfo = $this->findById($idOrUsername);
            } else {
                $userInfo = $this->where('username', '=', $idOrUsername)->find();
            }
            
            if ($userInfo) {
                foreach($userInfo as $key => $value) {
                    $this->$key = $value;
                }
            }
        }
    }

    public function findByUsername() {
        // validate input
        $validations = [
            new ValidationRule('Username', $this->username, [
                new RequiredRule()
            ]), 
            new ValidationRule('Password', $this->password, [
                new RequiredRule()                    
            ])
        ];
        $validator = new Validator($validations);
        if (!$validator->isValid()) {
            $this->_errorMessage = $validator->getValidationErrors();
            unset($validator);
            return;
        }
        unset($validator);

        return $this->where('username', '=', $this->username)->find();
    }

    public function login(string $password, bool $rememberMe = false) {
        if (!$this->_idField) {
            $this->_errorMessage = 'Please call the findByUsername method first.';
            return;
        }
        // verify password
        $encryptor = (object) $this->_passwordEncryptor;
        if ($encryptor instanceof IPasswordVerifier && !$encryptor->isVerified($password, $this->password)) {
            $this->_errorMessage = 'Invalid password.';
            return;
        }

        Session::set(SECURITY_CURRENT_LOGGED_IN_USER_ID, $this->{$this->_idField});
        self::$_isLoggedIn = true;
        

        if ($rememberMe) {
            $hash = md5(uniqid() . rand(0, 100));
            $userAgent = Session::getUserAgent();

            Cookie::set(SECURITY_COOKIE_REMEMBER_ME_NAME, $hash, SECURITY_COOKIE_EXPIRY);

            $this->startTransaction();
            $this->pdo = $this->database->pdo;

            try {
                // delete previous cookie stored
                $userSession = new UserSession();
                $userSession->pdo     = $this->pdo;
                $userSession->where('`user_id`', '=', $this->{$this->_idField})
                    ->_and()
                    ->where('`user_agent`', '=', $userAgent)
                    ->delete(null, true);
                if ($userSession->hasError()) {
                    $this->rollbackTransaction();
                    Cookie::delete(SECURITY_COOKIE_REMEMBER_ME_NAME);
                    $this->_errorMessage = $userSession->getErrorMessage();
                    unset($userSession);
                    return;
                }

                // capture user session
                $userSession->user_id = $this->{$this->_idField};
                $userSession->session = $hash;
                $userSession->user_agent = $userAgent;
                $userSession->save();
                if ($userSession->hasError()) {
                    $this->_errorMessage = $userSession->getErrorMessage();
                    $this->rollbackTransaction();
                    Cookie::delete(SECURITY_COOKIE_REMEMBER_ME_NAME);
                    unset($userSession);
                    return;
                }

                $this->commitTransaction();

                unset($userSession);
            } catch (PDOException $e) {
                $this->rollbackTransaction();
                $this->_errorMessage = (IS_DEVELOPMENT ? USER_FRIENDLY_ERROR_MESSAGE : $e->getMessage());
                $this->_logger->log($e->getMessage());
            } catch (Exception $e) {
                $this->rollbackTransaction();
                $this->_errorMessage = (IS_DEVELOPMENT ? USER_FRIENDLY_ERROR_MESSAGE : $e->getMessage());
                $this->_logger->log($e->getMessage());
            }
        }
    }

    public static function loginFromCookie() {
        if (Cookie::exists(SECURITY_COOKIE_REMEMBER_ME_NAME)) {
            $userSession = UserSession::getFromCookie();
            if ($userSession && $userSession->rowCount) {
                $user = new self($userSession->user_id);
                $user->login();
            }
        }
    }

    public static function isLoggedIn() {
        if (!self::getCurrentUser()) return false;

        return true;
    }

    public static function getCurrentUser() {
        if (Session::exists(SECURITY_CURRENT_LOGGED_IN_USER_ID)) {
            if (!self::$_currentLoggedInUser) {
                self::$_currentLoggedInUser = new self(Security::getCurrentUserId());
                self::$_isLoggedIn = true;
            }
        }
        
        return self::$_currentLoggedInUser;
    }

    public function logout() {
        if ($this->{$this->_idField}) {
            // delete previous cookie stored
            $userAgent = Session::getUserAgent();
            $userSession = UserSession::getFromCookie();
            if ($userSession) {
                $userSession->delete(null, true);
                unset($userSession);
            }
            if (Cookie::exists(SECURITY_COOKIE_REMEMBER_ME_NAME)) {
                Cookie::delete(SECURITY_COOKIE_REMEMBER_ME_NAME);
            }
            if (Session::exists(SECURITY_CURRENT_LOGGED_IN_USER_ID)) {
                Session::delete(SECURITY_CURRENT_LOGGED_IN_USER_ID);
            }
            self::$_currentLoggedInUser = null;
            self::$_isLoggedIn = false;
        }
    }
    
    public function register(string $confirmPassword) {        
        // validate input
        $validations = [
            new ValidationRule('Username', $this->username, [
                new RequiredRule(),
                new MinimumLengthRule(5),
                new UniqueRule($this, 'username')
            ]), 
            new ValidationRule('Email', $this->email, [
                new RequiredRule(),
                new EmailRule(),
                new UniqueRule($this, 'email')
            ]),
            new ValidationRule('Password', $this->password, [
                new RequiredRule(),
                new MinimumLengthRule(6)                    
            ]),
            new ValidationRule('Confirm password', $confirmPassword, [
                new RequiredRule(),
                new EqualRule($this->password, $confirmPassword)
            ])
        ];
        $validator = new Validator($validations);
        if (!$validator->isValid()) {
            $this->_errorMessage = $validator->getValidationErrors();
            unset($validator);
            return;
        }
        unset($validator);

        // create account
        $this->password = $this->_passwordEncryptor->encrypt($this->password);
        $this->save();
    }

    public function changePassword(int $id, string $password, string $confirmPassword) {
        // validations
        $validations = [
            new ValidationRule('Password', $password, [
                new RequiredRule(),
                new MinimumLengthRule(6),
                new MaximumLengthRule(20)
            ]),
            new ValidationRule('Confirm password', $confirmPassword, [
                new RequiredRule(),
                new EqualRule($password, $confirmPassword)
            ])
        ];
        $validator = new Validator($validations);
        if (!$validator->isValid()) {
            $this->_errorMessage = $validator->getValidationErrors();
            unset($validator);
            return;
        }
        unset($validator);

        if (!$this->findById($id)) {
            $this->_errorMessage = 'User information not found.';
            return;
        }

        $this->id       = $id;
        $this->password = $this->_passwordEncryptor->encrypt($this->password);
        $this->save();
    }
}