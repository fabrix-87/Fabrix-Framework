<?php

namespace App\Middlewares;

use System\Core\MiddlewareInterface;
use System\Http\Request;
use System\Helpers\{Session, Alerts};
use System\Routing\Routes;

class Auth implements MiddlewareInterface
{
    public function process(Request $requests)
    {
        if(Session::get('user_id')){            
            return true;
        }else{
            Alerts::set(Alerts::ERROR,'Devi effettuare l\'accesso per accedere alla pagina');
    		Session::set('from', $requests->getUri());
            Routes::redirect(Routes::route('admin.auth@index'));            
    		return false;
        }
    }
}