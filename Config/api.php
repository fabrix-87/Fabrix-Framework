<?php
/**
 * File di configurazione per l'accesso il modalità api
 */

return [
    // default middleware per tutte le rotte
    'middlewares' => [
        'JWT' => App\Middlewares\VerifyJWT::class,
    ],
    'JWT' => [
        'secret' => 'qualsiasiChiave',
        'algorithm' => 'HS256',
        'expire_time' => 300, // expire time in seconds
    ]
];