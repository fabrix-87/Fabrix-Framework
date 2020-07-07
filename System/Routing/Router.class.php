<?php

namespace System\Routing;

use Exception;
use System\Core\Registry;
use System\Routing\Routes;

/**
 * Gestione del Routing
 */
class Router
{    
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Carica la dichiarazione delle rotte per la modalità richiesta
     *
     * @param  string $mode (web|api)
     * @return void
     */
    public function initRoutes(string $mode)
    {
        Routes::getInstance();
        
        $path = ROUTES_PATH.$mode.'.php';
        try{            
            if(!file_exists($path))
                throw new Exception('Nessun file di dichiarazione delle rotte trovato per '.$mode);
            
            //$route = Routes::getInstance();            
            require_once($path);            

        }catch(Exception $e){
            //TODO: Gestione log degli errori
            die($e->getMessage());
        }       
    }

    /**
     * Istanzia ed avvia il controller con l'action indicata
     * @param string $action
     * @param string $prefix
     * @param array $args
     */
    public function executeAction(string $action, string $prefix = '', array $args = [])
    {
        if(stristr($action, '@') === FALSE) {
            return false;
        }

        list($controller, $method) = explode('@', $action);

        Routes::setCurrentRoute($action, $controller, $method, $prefix, $args);
            
        if($prefix === BACKEND_PREFIX){
            $prefix = BACKEND_FOLDER;
        }

        $prefix = ucfirst($prefix);

        if(empty($prefix) or !is_dir(CONTROLLER_PATH.$prefix)){
            $class = '\\App\\Controllers\\'.$controller.'_Controller';
        }else{
            $class = '\\App\\Controllers\\'.$prefix.'\\'.$controller.'_Controller';
        }

        if (!class_exists($class)) {
            throw new Exception('Controller "'.$class.'" non trovato.');
        }

        $controllerInstantiate = new $class($this->registry);

        if(!method_exists($controllerInstantiate, $method)){
            throw new Exception('Action "'.$method.'" non trovata.');
        }

        try{
            return call_user_func_array([$controllerInstantiate, $method], $args);            
        }catch(Exception $e) {
            include ASSETS_PATH.'static/500.html';
            exit();
        }
    }

    

}

