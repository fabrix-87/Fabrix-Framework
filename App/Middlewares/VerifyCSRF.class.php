<?php

namespace App\Middlewares;

use System\Core\MiddlewareInterface;
use System\Http\Request;
use System\Helpers\Session;

class VerifyCSRF implements MiddlewareInterface
{
    public function process(Request $requests)
    {
        if($requests->hasPost())
        {
            if($requests->post('CSRF') and Session::get('token')) 
            {
                if (!hash_equals(Session::get('token'), $requests->post('CSRF')))
                {
                    throw new \Exception("Token CSRF non valido", 1);
                }
            }else{
                throw new \Exception("Token CSRF non trovato", 1);
            }
        }
    }
}