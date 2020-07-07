<?php

namespace App\Controllers\Admin;

use System\Core\Controller;

class home_Controller extends Controller{

    protected $requireLogin = true;
    protected $accessLevel = 1;

    public function index(){
        $this->view->show('admin/home');
        return true;
    }

}

?>
