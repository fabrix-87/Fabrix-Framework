<?php

namespace System\Core;

use System\Core\Registry;
use System\Database\DB;
use System\{Config, SystemUser};
use System\Helpers\{Request, Session};
use System\Routing\{Dispatcher, Router};

use \Exception;

class System{
    public static function run() {
        self::init();
        self::autoload();

        $config = new Config();   

        $appCfg = $config->get('app');
        
        // Nome dell'app
        define('APP_NAME', $appCfg['name']);

        // Prefisso del backend (ex: admin)
        define('BACKEND_PREFIX', $appCfg['backend_prefix']);
        define('BACKEND_FOLDER', $appCfg['backend_subfolder']);
        

        $registry = new Registry();
        $registry->set('requests', new Request());
        $registry->set('config', $config);

        $router = new Router($registry);      
        $router->initRouting();

        $registry->set('db', new DB($config->get('database')));        

        $registry->set('user', new SystemUser($registry));

        // Instrada la route
        try{
          $router->startRoute();  
        }catch(Exception $e){
            die($e->getMessage());
        }
    }

    /**
     * Definisce le costanti per i percorsi
     */
    private static function init() {
        define("BASE_PATH", str_replace("//", "/", dirname($_SERVER['SCRIPT_NAME']).'/'));
        define("DS", DIRECTORY_SEPARATOR);
        define("ROOT", getcwd() . DS);

        /** @var string Percorso nel namespace dell'App */
        define('APP_FOLDER', 'App');

        /** @var string Percorso fisico della cartella App/ */
        define("APP_PATH", ROOT . ucfirst(APP_FOLDER) . DS); 

        define("CONFIG_PATH", ROOT . "Config" . DS);
        define("ROUTES_PATH", ROOT . "Routes" . DS);
        define("FRAMEWORK_PATH", ROOT . "System" . DS);
        define("PUBLIC_PATH", ROOT . "public" . DS);
        define("TEMPLATE_PATH", ROOT . "Templates" . DS);
        define("CONTROLLER_PATH", APP_PATH . "Controllers" . DS);
        define("MIDDLEWARE_PATH", APP_PATH . "Middlewares" . DS);

        /** @var string Percorso fisico della cartella dei Model */
        define("MODEL_PATH", APP_PATH . "Models" . DS);
        
        /** @var string Percorso nel namespace dei Mapper */
        define("MAPPER_FOLDER", APP_FOLDER . "\\Mappers\\");

        /** @var string Percorso fisico della cartella dei Mapper */
        define("MAPPER_PATH", APP_PATH . "Mappers" . DS);


        define("VIEW_PATH", TEMPLATE_PATH . "views" . DS);
        define("CORE_PATH", FRAMEWORK_PATH . "Core" . DS);
        define("ROUTING_PATH", FRAMEWORK_PATH . "Routing" . DS);
        define('DB_PATH', FRAMEWORK_PATH . "Database" . DS);
        define("LIB_PATH", FRAMEWORK_PATH . "Libraries" . DS);
        define("HELPER_PATH", FRAMEWORK_PATH . "Helpers" . DS);
        define("SERVICES_PATH", FRAMEWORK_PATH . "Services" . DS);
        define("DATA_PATH", FRAMEWORK_PATH . "Data" . DS);
        define("UPLOAD_PATH", PUBLIC_PATH . "uploads" . DS);
        define("ASSETS_PATH", ROOT . "assets" . DS);
    }


    private static function autoload() {
        spl_autoload_register(array(__CLASS__,'load'));
    }


    /**
     * Custom autoload delle classi
     */
    private static function load($className)
    {
        $fullName = $className;
        $includePath = '';

        //$className = str_replace('\\', DIRECTORY_SEPARATOR, $className);

        $path = explode('\\',$className);

        $className = end($path);

        $mainType = strtolower($path[0]);

        if($mainType == 'system'){
            switch(strtolower($path[1])){
                case 'core':
                    $includePath = CORE_PATH . "$className.class.php";
                    break;
                case 'database':
                    $includePath = DB_PATH . "$className.class.php";
                    break;
                case 'helpers':
                    $includePath = HELPER_PATH . "$className.class.php";
                    break;
                case 'libraries':
                    $includePath = LIB_PATH . "$className.class.php";
                    break;
                case 'services':
                    $includePath = SERVICES_PATH . "$className.class.php";
                    break;
                case 'data':
                    $includePath = DATA_PATH . "$className.class.php";
                    break;
                case 'routing':
                    $includePath = ROUTING_PATH . "$className.class.php";
                    break;
                default:
                    $includePath = FRAMEWORK_PATH . "$className.class.php";
            }
        }elseif($mainType == 'app'){
            $className = isset($path[3]) ? $path[2].DIRECTORY_SEPARATOR.$className : $className;
            switch(strtolower($path[1])){
                case 'controllers':
                    $includePath = CONTROLLER_PATH . $className .".php";
                    break;
                case 'models':
                    $includePath = MODEL_PATH . $className .".php";
                    break;
                case 'mappers':
                    $includePath = MAPPER_PATH . $className .".php";
                    break;
                case 'middlewares':
                    $includePath = MIDDLEWARE_PATH . $className .".class.php";
                    break;
                default:
                    $includePath = APP_PATH . ucfirst($className).".class.php";
            }
        }

        if(file_exists($includePath)){
            require_once($includePath);
        } else {
            throw new Exception("Classe $className non trovata in $includePath", E_USER_ERROR);
        }

    }



}
