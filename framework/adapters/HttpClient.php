<?php

namespace Framework\Adapters;

use Framework\Core\Json;
use Framework\Core\RequestHandlerResponse;
use Framework\Enums\RequestType;
use Framework\Interfaces\IHttpClient;
use Framework\Utils\Ary;
use Framework\Utils\Str;

final class HttpClient implements IHttpClient {

    private static $instance;

    private RequestType $_requestType;
    private string $_url;
    private array $_headers;
    private array $_payload;
    
    private function __construct() {}

    static function getInstance() : IHttpClient {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function setRequestType(RequestType $requestType) : IHttpClient {
        $this->_requestType = $requestType;
        return $this;
    }
    
    public function setUrl(string $url) : IHttpClient {
        $this->_url = $url;
        return $this;
    }

    public function setHeaders(array $headers) : IHttpClient {
        $this->_headers = $headers;
        return $this;
    }

    public function setPayload(array $payload) : IHttpClient {
        $this->_payload = $payload;
        return $this;
    }

    public function execute() : RequestHandlerResponse {
        // some validations
        $acceptableRequestTypes = RequestType::cases();
        if (!in_array($this->_requestType, $acceptableRequestTypes)) {
            return new RequestHandlerResponse(
                Json::STATUS_CODE_METHOD_NOT_ALLOWED,
                true,
                'Request type must be one of: ' . join(', ', $acceptableRequestTypes));            
        }
        if (!filter_var($this->_url, FILTER_VALIDATE_URL)) {
            return new RequestHandlerResponse(
                Json::STATUS_CODE_BAD_REQUEST,
                true,
                'Invalid url: ' . $this->_url);
        }
        if ($this->_requestType == RequestType::post && !$this->_payload) {
            return new RequestHandlerResponse(
                Json::STATUS_CODE_BAD_REQUEST,
                true,
                'Payload is expected for your ' . $this->_requestType . ' request.');
        }

        // prepare request
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $this->_url);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        if ($this->_requestType == RequestType::post) {
            curl_setopt($curlHandle, CURLOPT_POST, true);
        }
        if ($this->_requestType == RequestType::put) {
            curl_setopt($curlHandle, CURLOPT_PUT, true);
        }
        if ($this->_requestType == RequestType::delete) {
            curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, "DELETE");
        }

        // set header
        $jsonContentTypeKey = 'Content-Type';
        $jsonContentTypeValue = 'application/json';

        $jsonContentType = $jsonContentTypeKey.$jsonContentTypeValue;
        if (!$this->_headers) {
            $this->_headers  = [];
            array_push($this->_headers, $jsonContentType);
        } else {
            $hasContentType = false;
            if (Ary::isAssociative($this->_headers)) {
                $jsonContentType = [$jsonContentTypeKey => $jsonContentTypeValue];
                foreach($this->_headers as $key => $value) {
                    if (Str::contains($jsonContentTypeKey, Str::removeSpaces($key)) ||
                        Str::contains($jsonContentTypeValue, Str::removeSpaces($value))) {
                        $hasContentType = true;
                        break;
                    }
                }
            } else {
                foreach($this->_headers as $header) {
                    if ($jsonContentType == Str::removeSpaces($header)) {
                        $hasContentType = true;
                        break;
                    }
                }
            }

            if (!$hasContentType) {
                $this->_headers[$jsonContentTypeKey] = $jsonContentTypeValue;
            }
        }
        if ($this->_headers) {
            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $this->_headers);
        }
        if ($this->_payload) {
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, json_encode($this->_payload, JSON_PRETTY_PRINT));
        }

        $curlExecution = curl_exec($curlHandle);
        if ($curlExecution === false) {
            return new RequestHandlerResponse(
                Json::STATUS_CODE_INTERNAL_SERVER_ERROR,
                true,
                'Request error: ' . curl_error($curlHandle));
        } else {
            $result = json_decode($curlExecution, true);
            $statusCode = curl_getinfo($curlHandle, CURLINFO_RESPONSE_CODE);
            curl_close($curlHandle);

            return new RequestHandlerResponse(
                $statusCode,
                false,
                strval($statusCode),
                $result);
        }

        return new RequestHandlerResponse(
            Json::STATUS_CODE_INTERNAL_SERVER_ERROR,
            true,
            'Unable to handle your request.');
    }

}