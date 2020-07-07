<?php

namespace App\Controllers\Admin;

use System\Core\Controller;
use System\Core\Router;
use System\Services\AuthService;
use System\Helpers\{Alerts, Session};


/**
 * Controller per la gestione degli accessi degli utenti
 */
class auth_Controller extends Controller
{

    public function index()
    {
        if(!$this->user->isLogged()){
            $this->view->show('admin/login');
        }else{
            Router::redirect(Router::route('admin.home@index'));
        }
    }

    public function login()
    {
        if(!$this->user->isLogged()){
            $res = $this->user->login($this->requests->post('username'), $this->requests->post('password'));
            switch ($res) {
                case 'ok':
                    $url = Session::get('from') ?: Router::route('admin.home@index');
                    Session::remove('from');
                    Router::redirect($url);
                    break;
                case 'err_pwd':
                    Alerts::set(Alerts::ERROR, 'Password errata');
                    Router::redirect(Router::route('admin.auth@index'));
                    break;
                default:
                    Alerts::set(Alerts::ERROR, 'Username non presente');
                    Router::redirect(Router::route('admin.auth@index'));
                    break;
            }
        }else{
            Router::redirect(Router::route('admin.home@index'));
        }
    }

    public function logout()
    {
        $this->user->logout();
        Router::redirect(Router::route('admin.auth@index'));
    }
}


 ?>
