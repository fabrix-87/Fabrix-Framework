<?php

namespace System\Routing;

use Exception;
use System\Core\Registry;
use System\Helpers\Session;
use System\Routing\Routes;

/**
 * Gestione del Routing
 */
class Router
{    
    private Registry $registry;
    private $mode = 'web';
    private $dispatchedRoute;
    private $config;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;        
    }

    public function initRouting()
    {
        $requests = $this->registry->get('requests');
        $this->mode = $requests->getRequestMode();

        // carico la definizione delle route
        $this->loadRoutes($this->mode);

        // Effettuo il dispatch della route
        $dispatcher = new Dispatcher();
        $this->dispatchedRoute = $dispatcher->dispatch($requests->getHttpMethod(), $requests->getPath());

        // Avvio la sessione.
        $this->config = $this->registry->get('config');
        $sessionCfg = $this->config->get('session');

        $sessionName = ($this->dispatchedRoute['prefix'] == BACKEND_PREFIX) ? $sessionCfg['backend_name'] : $sessionCfg['frontend_name'];        

        Session::start($sessionCfg, $sessionName);
    }

    public function startRoute()
    {
        // eseguo i middleware prima di richiamare il controller
        $modeCfg = $this->config->get($this->mode);
        $middlewares = array_merge( $modeCfg['middlewares'],  $this->dispatchedRoute['middleware']);
        foreach($middlewares as $middleware)
        {
            if(class_exists($middleware))
            {
                new $middleware($this->registry);
            }            
        }

        $response = $this->executeAction(
            $this->dispatchedRoute['action'], 
            $this->dispatchedRoute['prefix'], 
            $this->dispatchedRoute['args']
        );
        
        if($this->mode === 'api' and is_array($response)){
            header('Content-Type: application/json');
            echo json_encode($response);
        }
    }

    /**
     * Carica la dichiarazione delle rotte per la modalità richiesta
     *
     * @param  string $mode (web|api)
     * @return void
     */
    private function loadRoutes(string $mode)
    {
        $path = ROUTES_PATH.$mode.'.php';
        try{            
            if(!file_exists($path))
                throw new Exception('Nessun file di dichiarazione delle rotte trovato per '.$mode);
            
            Routes::getInstance();            
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
    private function executeAction(string $action, string $prefix = '', array $args = [])
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

