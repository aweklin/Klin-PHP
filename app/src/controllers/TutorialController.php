<?php

namespace App\Src\Controllers;

use Framework\Core\Controller;
use Framework\Adapters\MailClient;

class TutorialController extends Controller {

    public function index() {
        $this->response->view();
    }

    public function project_structure() {
        $this->response->view('tutorial/project_structure');
    }

}