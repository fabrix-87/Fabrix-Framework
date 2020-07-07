<?php

/**
 * Definire qui sotto le route per le api
 *
 * "/user/{name}/{id:[0-9]+}?"
 *
 * Shortcuts
 *
 * :i => :/d+                # numbers only
 * :a => :[a-zA-Z0-9]+       # alphanumeric
 * :c => :[a-zA-Z0-9+_\-\.]+  # alnumnumeric and + _ - . characters
 * :h => :[a-fA-F0-9]+       # hex
 *
 * use in routes:
 *
 * '/user/{name:i}'
 * '/user/{name:a}'
 */

$route->group(['prefix' => BACKEND_PREFIX], function($route){
    $route->resource('users');
});

$route->get('test','test@prova');