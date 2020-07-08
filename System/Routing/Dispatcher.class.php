<?php

namespace System\Routing;

use \Exception;

class Dispatcher {
    protected $defaultPath = 'home';
    protected $staticRoutes = [];
    protected $dynamicRoutes = [];

    public function __construct() {
        list($this->staticRoutes, $this->dynamicRoutes) = Routes::getRoutes();
    }


    public function dispatch($httpMethod, $uri)
    {
        // Controlla che il metodo della richiesta sia valido
        if(!in_array($httpMethod, Routes::getValidMethods()))
        {
            throw new Exception('Http method '.$httpMethod.' non valido');
        }

        try{
            return $this->dispatchRoute($httpMethod, trim($uri,'\\/'));
        }catch(Exception $e) {
            include ASSETS_PATH.'static/404.html';
            exit();
        }

    }


    private function dispatchRoute($httpMethod, $uri): array
    {
        $uri = !empty($uri) ? $uri : $this->defaultPath;

        // controllo se l'uri inserito fa parte di una route statica
        if(isset($this->staticRoutes[$uri]))
        {
            $route = $this->staticRoutes[$uri];

            if(!isset($route[$httpMethod])){
                $httpMethod = $this->checkFallbacks($route, $httpMethod);
            }
            return $route[$httpMethod];
        }else{
            // altrimenti cerco tra le route dinamiche
            foreach ($this->dynamicRoutes as $regex => $route)
            {
                $regex = str_replace('/','\\/',$regex);
                if (!preg_match('/'.$regex.'/', $uri, $matches))
                {
                    continue;
                }

                if (!isset($route[$httpMethod]))
                {
                    $httpMethod = $this->checkFallbacks($route, $httpMethod);
                }

                // assegno i valori delle variabili richieste nella route
                foreach(array_values($route[$httpMethod]['args']) as $i => $varName)
                {
                    if(!isset($matches[$i + 1]) || $matches[$i + 1] === '')
                    {
                        // se non è stato inserito il dato elimino la variabile
                        unset($route[$httpMethod]['args'][$varName]);
                    }
                    else
                    {
                        $route[$httpMethod]['args'][$varName] = $matches[$i + 1];
                    }
                }

                return $route[$httpMethod];
            }

        }

        throw new \Exception("La route '$uri' non esiste", 1);
    }

    /**
     * Check fallback routes: HEAD for GET requests followed by the ANY attachment.
     *
     * @param $routes
     * @param $httpMethod
     * @throws Exception
     */
    private function checkFallbacks($routes, $httpMethod)
    {
        $additional = array('ANY');

        if($httpMethod === 'HEAD')
        {
            $additional[] = 'GET';
        }

        foreach($additional as $method)
        {
            if(isset($routes[$method]))
            {
                return $method;
            }
        }

        $this->matchedRoute = $routes;

        throw new Exception('Permessi: ' . implode(', ', array_keys($routes)));
    }



    protected function convertToStudlyCaps($string) {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }
    protected function convertToCamelCase($string) {
        return lcfirst($this->convertToStudlyCaps($string));
    }
    protected function removeQueryStringVariables($url) {
        $parts = explode('?', $url);
        return $parts[0];
    }
}
