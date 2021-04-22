<?php

namespace App\Src\Controllers;

use Framework\Core\Controller;

class ErrorController extends Controller {

    function notFound() {
        $this->response->view('error/not_found');
    }

}