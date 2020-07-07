<?php

namespace App\Controllers;

use System\Core\Controller;

/**
 *
 */
class home_Controller extends Controller
{

    public function index()
    {
        $this->view->show('home');
        return true;
    }
}



 ?>
