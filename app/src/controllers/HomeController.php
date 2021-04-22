<?php

namespace App\Src\Controllers;

use Framework\Core\{App, Controller, Response};
use Framework\Utils\Mail;
use App\Src\Models\User;

class HomeController extends Controller {

    public function index() {        
        $this->response->view();
    }

}