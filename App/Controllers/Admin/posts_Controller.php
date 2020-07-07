<?php

namespace App\Controllers\Admin;

use System\Core\Controller;

class posts_Controller extends Controller{

    protected $requireLogin = true;

    public function index(){

        echo 'saro';
        return true;
    }

    public function create(){

        echo 'nuovo post';
        return true;
    }

}