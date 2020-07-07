<?php
/**
 *
 * @version 1.0
 * @autor Menza Fabrizio
 *
 **/

namespace System\Core;

use Exception;
use System\Core\{View, Router, Registry};
use System\Data\DataMapper;
use System\Helpers\{Session, Alerts};

class Controller{
    protected $registry = null;
    protected $model = null;
    protected $mapper = null;
    protected $config = null;
    protected $requests = null;
    protected $view;
    protected $request;
    protected $requireLogin = false;
    protected $accessLevel = 0;
    protected $user;

    /** @var string Custom name of the model (default the same of the controller) */
    protected $modelName = null; 

    /** @var string Custom name of the mapper (default the same of the controller) */
    protected $mapperName = null; 

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
        $this->config = $registry->get('config');
        $this->requests = $registry->get('requests');
        $this->user = $registry->get('user');

        if($this->requireLogin){
            $this->requireLogin();
        }

        // Se non è stato definito assegno al nome del modello il nome del controller
        if(is_null($this->modelName))
        {
            $className = explode('\\',get_class($this));
            $className = end($className);
    
            $this->modelName = str_replace('_Controller','',$className);     
        }

        $modelClassName = APP_FOLDER.'\\'.ucfirst($this->modelName);

        // Se esiste un model associato al controller lo inizializzo
        try{
            
            $this->model = new $modelClassName();

            // Se c'è un dataMapper personalizzato lo carico altrimenti uso quello generale

            // Se non è stato definito un nome per il mapper, gli assegno lo stesso del model
            if(is_null($this->mapperName))
            {
                $this->mapperName = $this->modelName;
            }

            $mapperName = MAPPER_FOLDER.$this->mapperName.'_Mapper';

            try{
                $this->mapper = new $mapperName($this->registry->get('db'), $this->model);
            }catch(Exception $e){
                $this->mapper = new DataMapper($this->registry->get('db'), $this->model);
            }
        }catch(Exception $e){
            // model non trovato (fa niente)

        }

        $this->view = new View($registry);

    }

	public function requireLogin()
	{
        $ok = false;

        if($this->user->isLogged()){
            if($this->accessLevel <= $this->user->getUserLevel()){
                $ok = true;
            }else{
                $this->user->logout();
                Alerts::set(Alerts::ERROR,'Permessi insufficenti per accedere alla pagina');
            }
        }else{
            Alerts::set(Alerts::ERROR,'Devi effettuare l\'accesso per accedere alla pagina');
        }

        if(!$ok){
            $routeInfo = $this->registry->get('router')->currentRoute();
            $prefix = empty($routeInfo['prefix']) ? '' : $routeInfo['prefix'].'.';
    		Session::set('from', $this->requests->getUri());
    		Router::redirect(Router::route($prefix.'auth@index'));
    		exit(0);
        }

	}

}

?>
