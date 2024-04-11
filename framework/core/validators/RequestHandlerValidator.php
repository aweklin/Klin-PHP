<?php

namespace Framework\Core\Validators;

use Framework\Core\Attributes\RequestForgeryValidatorAttribute;
use Framework\Exceptions\InvalidRequestException;
use Framework\Infrastructure\Session;
use Framework\Interfaces\IRequest;
use Framework\Utils\Str;
use ReflectionClass;

class RequestHandlerValidator {

    private array $_handlers = [];

    public function register(array $requestHandlers) {
        foreach($requestHandlers as $requestHandler) {
            $reflectionClass = new ReflectionClass($requestHandler);
            $attributes = $reflectionClass->getAttributes(RequestForgeryValidatorAttribute::class);

            foreach($attributes as $attribute) {
                $requestForgeryValidator = $attribute->newInstance();
                array_push($this->_handlers, $requestForgeryValidator);
            }
        }
    }

    public static function validate(IRequest $request) {
        if (!in_array(Str::toLower($request->getMethod()), ['post', 'put', 'delete']))
            return;

        if (!Session::exists(SECURITY_FORM_TOKEN))
            throw new InvalidRequestException();

        if (!$request->get(SECURITY_FORM_TOKEN))
            throw new InvalidRequestException();

        $sessionToken = Session::get(SECURITY_FORM_TOKEN);
        $requestToken = $request->get(SECURITY_FORM_TOKEN);
        if ($sessionToken !== $requestToken)
            throw new InvalidRequestException();
    }

}