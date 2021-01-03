<?php

namespace System\Core;

use System\Http\Request;

// TODO: usare PSR
interface MiddlewareInterface
{
    public function process(Request $request);
}