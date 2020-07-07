<?php
/**
 * File di configurazione delle sessioni e cookie
 */

return [
    'frontend_name' => 'front', // session name of the frontend
    'backend_name' => 'back', // session name of the backend. If the name is the same of frontend the session will be shared.
    'lifetime' => 604800, // Lifetime of the session cookie, defined in seconds.
    'path' => '/', // Path on the domain where the cookie will work. Use a single slash ('/') for all paths on the domain.
    'domain' => '',
    'secure' => TRUE, // If TRUE cookie will only be sent over secure connections.
    'http_only' => TRUE,
];


?>
