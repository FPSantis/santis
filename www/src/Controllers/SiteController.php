<?php

namespace App\Controllers;

use App\Core\BaseController;

class SiteController extends BaseController {
    
    public function index() {
        $this->render('home');
    }

    public function blog() {
        $this->render('blog');
    }

    public function post($id) {
        $this->render('post', ['postId' => $id]);
    }
}
