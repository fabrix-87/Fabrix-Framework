<?php

namespace App\Middlewares;

use System\Core\MiddlewareInterface;
use System\Http\Request;
use \Firebase\JWT\JWT;


class VerifyJWT implements MiddlewareInterface
{
    public function process(Request $requests)
    {
    }
}