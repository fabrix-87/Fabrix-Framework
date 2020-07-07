<?php

namespace System\Helpers;

use System\Helpers\Session;

class Alerts
{
    const ERROR = 'danger';
    const INFO = 'primary';
    const WARNING = 'warning';
    const SUCCESS = 'success';

    /**
     * Set an alert message in to the session
     * @param string $type    Type of the alert
     * @param string $message Message to display
     */
    public static function set(string $type, string $message): void
    {
        $alerts = Session::get('alerts');
        if(!$alerts) $alerts = [];

        $alerts[] = [
            'type' => $type,
            'message' => $message
        ];

        Session::set('alerts', $alerts);
    }

    /**
     * Get an array with all alert messages {$type, $message}
     * @return Array|false
     */
    public static function gets()
    {
        $alerts = Session::get('alerts');
        Session::remove('alerts');
        return $alerts;
    }
}



 ?>
