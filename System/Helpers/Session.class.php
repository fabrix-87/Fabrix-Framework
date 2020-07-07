<?php

namespace System\Helpers;

class Session {
    private static $istance = null;
    private static $conf = array();

    private function __construct(array $config)
    {
        self::$conf = $config;
    }

    public static function start(array $conf, $name = 'web')
    {
        if(!is_null(self::$istance))
            return self::$istance;
        else
            self::$istance = new Session($conf);

        session_set_cookie_params(
            self::$conf['lifetime'],
            self::$conf['path'],
            self::$conf['domain'],
            self::$conf['secure'],
            self::$conf['http_only']
        );
        session_name($name);
        session_start();

    }

    public static function set($name, $sv) {
        // Sets the session variable
        $_SESSION[$name] = $sv;
    }

    public static function getAll() {
        // gets the session array with all variables
        return $_SESSION;
    }

    public static function get($name) {
        // displays the session variable
        return isset($_SESSION[$name]) ? $_SESSION[$name] : false;
    }

    public static function remove($name) {
        // removes session variable
        unset($_SESSION[$name]);
    }

    public static function regenerate() {
        // regenerates the id of session
        session_regenerate_id();
    }

    public static function killSession() {
        // kills the sessions.
        // to be used on logouts for example.
        $_SESSION = array();
        session_destroy();
    }
}



?>
