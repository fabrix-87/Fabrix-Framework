<?php

namespace System\Services;

use App\Mappers\user_Mapper;
use System\Core\{Request, Registry};
use System\Data\DataModel;
use System\Helpers\Session;


/**
 *
 */
class AuthService
{
    private $user;
    private $uMap;

    function __construct(DataModel $user, user_Mapper $uMap)
    {
        $this->user = $user;
        $this->uMap = $uMap;
    }

    public function init() {
        if(Session::get('user_id')){
            $this->uMap->findById(Session::get('user_id'), $this->user);
            return true;
        }
        return false;
    }

    /**
     * Effettua il login con mail e password passati da parametro
     * @param  string $username Nome utente
     * @param  string $pwd      password come da input utente
     * @return string           Ritorna lo status del login (ok, err_pwd, err_usr)
     */
    public function login(string $username, string $pwd): string
    {
        $res = $this->uMap->getPassword($username);
        if($res){
            if(password_verify($pwd, $res[1])){
                Session::set('user_id', $res[0]);
                return 'ok';
            }
            return 'err_pwd';
        }
        return 'err_usr';
    }

    public function logout()
    {
        Session::remove('user_id');
    }
}



 ?>
