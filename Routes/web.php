<?php

/**
 * Definire qui sotto le route del sito
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

use System\Routing\Routes;

Routes::get('home','home@index');
Routes::get('test','test@index');
Routes::get('ciccio.html','test@index');
//Routes::get('user','user@index');
//Routes::auth();

Routes::group(['prefix' => BACKEND_PREFIX], function(){
    Routes::group(['middleware' => 'Auth'],function(){
        Routes::get('home','home@index');
        Routes::get('/','home@index');
        Routes::resource('users');
        Routes::resource('posts');
    });
    Routes::auth();
});


/*
 var_dump(Routes::getRoutes());
 die();
*/
?>
