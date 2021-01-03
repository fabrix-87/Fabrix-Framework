<?php

namespace App\Controllers\Admin;

use System\Core\Controller;
use System\Routing\Routes;
use System\Helpers\{Alerts, Session};
use \Firebase\JWT\JWT;


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
            Routes::redirect(Routes::route('admin.home@index'));
        }
    }

        
    /**
     * Effettua l'autenticazione
     *
     * @return Array
     */
    private function signIn(): Array
    {
        $data = [
            'status' => '',
            'message' => '',
        ];

        $res = $this->user->login($this->requests->post('username'), $this->requests->post('password'));
        switch ($res) {
            case 'ok':
                $data['status'] = 'success';
                break;
            case 'err_pwd':
                $data['status'] = 'error';
                $data['message'] = 'Password errata';
                break;
            default:
                $data['status'] = 'error'; 
                $data['message'] = 'Utente non presente';
                break;
        }          
        return $data;
    }

        
    /**
     * Login via web
     *
     * @return void
     */
    public function login()
    {

        if(!$this->user->isLogged()){
            $res = $this->signIn();
            if($res['status'] === 'success'){
                    $url = Session::get('from') ?: Routes::route('admin.home@index');
                    Session::remove('from');
                    Routes::redirect($url);
            }else{
                Alerts::set(Alerts::ERROR, $res['message']);
                Routes::redirect(Routes::route('admin.auth@index'));
            }
        }else{
            Routes::redirect(Routes::route('admin.home@index'));
        }
    }

    /**
     * Login via api
     *
     * @return void
     */
    public function apiLogin()
    {
        $res = $this->signIn();
        if($res['status'] === 'success')
        {
            $cfg = $this->registry->get('config')->get('api');
            $secret_key = $cfg['JWT']['secret'];
            $issuer_claim = "THE_ISSUER"; // this can be the servername
            $audience_claim = "THE_AUDIENCE";
            $issuedat_claim = time(); // issued at
            $notbefore_claim = $issuedat_claim + 10; //not before in seconds
            $expire_claim = $issuedat_claim + $cfg['JWT']['expire_time']; // expire time in seconds
            $token = array(
                //"iss" => $issuer_claim,
                //"aud" => $audience_claim,
                "iat" => $issuedat_claim,
                "nbf" => $notbefore_claim,
                "exp" => $expire_claim,
                "data" => array(
                    "firstname" => $this->user->firstname,
                    "lastname" => $this->user->lastname,
                    "email" => $this->user->email
            ));

            http_response_code(200);

            $jwt = JWT::encode($token, $secret_key);
            return [
                "message" => "Successful login.",
                "token" => $jwt,
                "expireAt" => $expire_claim,
                "email" => $this->user->email,
            ];
        }else{
            http_response_code(401);
            return ['message' => $res['message']];
        }
    }

    public function logout()
    {
        $this->user->logout();
        Routes::redirect(Routes::route('admin.auth@index'));
    }
}


 ?>
