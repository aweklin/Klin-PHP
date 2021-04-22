<?php

namespace App\Src\Models;

use Framework\Core\Model;
use Framework\Infrastructure\{Cookie, Session};
use App\Src\Models\User;

class UserSession extends Model {

    public static function getFromCookie() {
        if (Cookie::exists(SECURITY_COOKIE_REMEMBER_ME_NAME)) {
            $userSessionModel = new self();
            $userSession = $userSessionModel
                ->where('user_agent', '=', Session::getUserAgent())
                ->_and()
                ->where('session', '=', Cookie::get(SECURITY_COOKIE_REMEMBER_ME_NAME))
                ->findObject();
            
            unset($userSessionModel);

            return $userSession;
        }

        return null;
    }

}