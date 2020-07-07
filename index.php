<?php

/**
 *
 * @version 0.1b
 * @autor Menza Fabrizio
 *
 * */

require_once 'System/Core/System.class.php';

use \System\Core;

if (version_compare(PHP_VERSION, '7.3.0') < 0) {
    echo 'Ho bisogno almeno della versione 7.3.0 di PHP per continuare. Versione attuale: ' . PHP_VERSION . "\n";
    exit(0);
}

Core\System::run();

//echo System\Config::DB_HOST;

exit(0);
?>
