<?php
/**
 * File di configurazione per l'accesso il modalità web
 */

return [
    // default middleware per tutte le rotte
    'middlewares' => [
        'CSRF' => App\Middlewares\VerifyCSRF::class,
    ],
];