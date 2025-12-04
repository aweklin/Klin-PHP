<?php

namespace Framework\Core;

require_once dirname(__FILE__, 2) . DS . 'libs' . DS . 'vendor' . DS . 'autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Framework\Utils\Str;

class ApiController extends Controller {

  private array $jwtPayload = [
      'iss' => JWT_AUTHOR,
      'aud' => JWT_AUTHOR
  ];

  public function __construct(string $controller, string $action) {
    parent::__construct($controller, $action);
    $this->request->stripHtmlFromValidationErrors(true);
  }

  protected function validateAuthorization() {
    $jwt = $this->getBearerToken();
    if (!$jwt) {
      return $this->response->json->unauthorized('Access token is missing.');
    }

    if (!$this->isJwtValid($jwt)) {
      return $this->response->json->unauthorized('Access token is invalid or has expired.');
    }
  }

  protected function generateJWT(array $data) : string {
    $currentTime = time();
    $payload = array_merge(
        $this->jwtPayload, 
        [
          'jti' => uniqid(Str::generateRandomString()),
          'nbf' => $currentTime,
          'iat' => $currentTime,
          'exp' => $currentTime + JWT_TTL,
        ],
        $data);
    $jwt = JWT::encode($payload, JWT_KEY, JWT_ALGORITHM);

    return $jwt;
  }

  protected function decodeJWT(string $token) : mixed {
    return JWT::decode($token, new Key(JWT_KEY, JWT_ALGORITHM));
  }

  protected function getBearerToken() : string|null {
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
      $headers = trim($_SERVER["Authorization"]);
    } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
      $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
      $requestHeaders = apache_request_headers();
      // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
      $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
      if (isset($requestHeaders['Authorization'])) {
        $headers = trim($requestHeaders['Authorization']);
      }
    }
    // HEADER: Get the access token from the header
    if (!empty($headers)) {
      if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
        return $matches[1];
      }
    }
    return null;
  }

  protected function isJwtValid(string $token) : bool {
    try {
      $decoded = $this->decodeJWT($token);
      if ($decoded && isset($decoded->exp) && ($decoded->exp >= time())) {
        return true;
      }
    } catch (\Exception $ex) {
      $this->logger->error('JWT validation error: ' . $ex->getMessage());
    }
    return false;
  }

  protected function getJwtComponent(string $component) : mixed {
    try {
      $decoded = $this->decodeJWT($this->getBearerToken());
      if ($decoded && isset($decoded->{$component})) {
        return $decoded->{$component};
      }
    } catch (\Exception $ex) {
      $this->logger->error('JWT component retrieval error: ' . $ex->getMessage());
    }
    return null;
  }
}