<?php

namespace App\Src\Controllers\Handlers;

use App\Src\Models\User;
use Exception;
use Framework\Core\Json;
use Framework\Core\RequestHandlerResponse;
use Framework\Core\Validators\Rules\RequiredRule;
use Framework\Enums\LogLevel;
use Framework\Exceptions\ExceptionBase;
use Framework\Interfaces\{ILogger, IRequest, IRequestHandler};

class LoginRequestHandler implements IRequestHandler {

    public function __construct(private User $_user, private ILogger $_logger) {}

    public function invoke(IRequest $request) : RequestHandlerResponse {
        if (!$request->isValid([
                'username' => [new RequiredRule()], 
                'password' => [new RequiredRule()]]))
            return new RequestHandlerResponse(Json::STATUS_CODE_BAD_REQUEST, true, $request->getValidationErrors());        

        $this->_user->username = $request->get('username');
        $this->_user->password = $request->get('password');
        
        try {
            $userInfo = $this->_user->findByUsername();
            if ($this->_user->hasError())
                return new RequestHandlerResponse(Json::STATUS_CODE_BAD_REQUEST, true, $this->_user->getErrorMessage());
                
            $rememberMe = $request->get('remember_me');                    
            $rememberMe = ($rememberMe ? true : false);
            
            if ($userInfo && $this->_user->login($request->get('password'), $rememberMe)) {
                if ($this->_user->hasError())
                    return new RequestHandlerResponse(Json::STATUS_CODE_BAD_REQUEST, true, $this->_user->getErrorMessage());

                return new RequestHandlerResponse(Json::STATUS_CODE_OK, false, 'Login successful');                
            }
                
            return new RequestHandlerResponse(Json::STATUS_CODE_BAD_REQUEST, true, 'Incorrect username or password.');
        } catch (Exception $e) {
            $this->_logger->log(LogLevel::ERROR, ExceptionBase::convertExceptionToStringForLogging($e));
            return new RequestHandlerResponse(Json::STATUS_CODE_INTERNAL_SERVER_ERROR, true, $e->getMessage());
        }
    }
}