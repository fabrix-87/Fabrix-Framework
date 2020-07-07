<?php

namespace System\Routing;

use Exception;
use InvalidArgumentException;
use System\Routing\RouteParser;

class Routes
{
    private static $instance = null;

    // contine i dati della route attuale (action, prefix, args)
    private static $currentRoute = [];

    // parser della route
    private static $parser = null;

    // prefisso per le classi dei controller
    private static $prefix = '';

    private static $staticRoutes = [];
    private static $dynamicRoutes = [];

    // Contiene le route invertite. Da action a uri
    private static $reverse = [];

    private function __construct()
    {
        self::$parser = new RouteParser();
    }

    public static function getInstance()
    {
        if(self::$instance == null)
        {   
            self::$instance = new Routes();
        }
        
        return self::$instance;
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     */
    private function __wakeup()
    {
    }

        
    /**
     * Setta le informazioni della route attuale
     *
     * @param  string $action
     * @param  string $controller
     * @param  string $method
     * @param  string $prefix
     * @param  array $args
     * @return void
     */
    public static function setCurrentRoute(string $action, string $controller, string $method, string $prefix, array $args): void
    {
        self::$currentRoute = [
            'action' => $action,
            'controller' => $controller,
            'method' => $method,
            'prefix' => $prefix,
            'args' => $args,
        ];
    }

    public static function currentRoute()
    {
        return self::$currentRoute;
    }

    public static function currentPrefix()
    {
        return self::$currentRoute['prefix'].'.';
    }

    /**
     * Genera le uri dinamicamente
     * @param  string $action (prefix.controller@action) | il prefisso admin funziona da alias di BACKEND_PREFIX
     * @param  array $args
     * @return string
     */
    public static function route(string $action, array $args = null): string
    {
        // Sostituisco il prefix 'admin' con quello reale settato nella configurazione
        $re = '/^admin./m';
        $action = preg_replace($re, BACKEND_PREFIX.'.', $action);

        if(!isset(self::$reverse[$action])){
            throw new Exception("Route '{$action}' not found");
        }

        $url = [];
        $replacements = is_null($args) ? [] : array_values($args);
        $variable = 0;
        foreach(self::$reverse[$action]['data'] as $part)
        {
            if(!$part['variable'])
            {
                $url[] = $part['value'];
            }
            elseif(isset($replacements[$variable]))
            {
                if($part['optional'])
                {
                    $url[] = '/';
                }
                $url[] = $replacements[$variable++];
            }
            elseif(!$part['optional'])
            {
                throw new Exception("Expecting route variable '{$part['name']}'");
            }
        }

        $prefix = isset(self::$reverse[$action]['prefix']) ? self::$reverse[$action]['prefix'] : '';

        return preg_replace('/([^:])(\/{2,})/', '$1/', BASE_PATH.$prefix.'/'.implode('', $url).'/');
    }

    /**
     * Esegue il redirect di una url
     * @param string $url
     */
    public static function redirect(string $url): void
    {
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        header('Location: '.$protocol.str_replace("//", "/", $_SERVER['SERVER_NAME'].$url));
    }


    /**
     * Aggiunge una route ES: path, route@action, web|admin
     * @param string $httpMethod
     * @param string $path
     * @param string $action
     */
    private static function addRoute(string $httpMethod, string $path, string $action)
    {
        if(!in_array($httpMethod, self::getValidMethods())){
            die('no');
        }
        list($routeData, $reverseData) = self::$parser->parse($path);

        if(!empty(self::$prefix)){
            self::$reverse[self::$prefix.'.'.$action] = ['prefix' => self::$prefix, 'data' => $reverseData];
        }else{
            self::$reverse[$action] = ['data' => $reverseData];
        }

        if(!isset($routeData[1])){
            self::addStaticRoute($httpMethod, $routeData, $action);
        }else{
            self::addDynamicRoute($httpMethod, $routeData, $action);
        }

    }

    
    private static function addStaticRoute(string $httpMethod, array $routeData, string $action)
    {
        $route = self::addPrefix($routeData[0]);

        if(isset(self::$staticRoutes[$route][$httpMethod]))
        {
             throw new Exception("Impossibile registrare due route '$route' per il metodo '$httpMethod'");
        }

        self::$staticRoutes[$route][$httpMethod] = [
            'prefix' => self::$prefix,
            'action' => $action,
            'args' => [],
            ];
    }


    private static function addDynamicRoute(string $httpMethod, array $routeData, string $action)
    {
        list($regex, $variables) = $routeData;

        $regex = self::addPrefix($regex);

        if(isset(self::$dynamicRoutes[$regex][$httpMethod]))
        {
             throw new Exception("Impossibile registrare due route '$regex' per il metodo '$httpMethod'");
        }

        self::$dynamicRoutes[$regex][$httpMethod] = [
            'prefix' => self::$prefix,
            'action' => $action,
            'args' => $variables,
            ];
    }

    /**
     * Aggiunge il prefisso attuale alla route
     * @param string $route
     * @return string $route
     */
    private static function addPrefix(string $route): string
    {
        return self::trimSlashes(self::$prefix . '/' . $route) ;
    }

    /**
     * Rimuove gli slash nella stringa
     * @param string $path
     * @return string
     */
    private static function trimSlashes(string $path): string
    {
        return trim($path,'\\/');
    }

    /**
     * Aggiunge una route con il metodo http GET
     * @param string $path
     * @param string $route
     */
    public static function get(string $path, string $route)
    {
        self::addRoute('GET', $path, $route);
    }

    /**
     * Aggiunge una route con il metodo http POST
     * @param string $path
     * @param string $route
     */
    public static function post(string $path, string $route)
    {
        self::addRoute('POST', $path, $route);
    }

    /**
     * Aggiunge una route con il metodo http PUT
     * @param string $path
     * @param string $route
     */
    public static function put(string $path, string $route)
    {
        self::addRoute('PUT', $path, $route);
    }

    /**
     * Aggiunge una route con il metodo http DELETE
     * @param string $path
     * @param string $route
     */
    public static function delete(string $path, string $route)
    {
        self::addRoute('DELETE', $path, $route);
    }

    /**
     * Aggiunge una route con qualsiasi metodo Http
     * @param string $path
     * @param string $route
     */
    public static function any(string $path, string $route)
    {
        self::addRoute('ANY', $path, $route);
    }

    /**
     * Crea tutte le route di una classe resource GET, POST, PUT, DELETE in automatico
     * @param string $name
     */
    public static function resource(string $name)
    {
        self::get($name, $name.'@index');
        self::get($name.'/{id:i}', $name.'@show');
        self::get($name.'/{id:i}/edit', $name.'@edit');
        self::get($name.'/create', $name.'@create');
        self::post($name.'/store', $name.'@store');
        self::put($name.'/{id:i}', $name.'@update');
        self::delete($name.'/{id:i}', $name.'@destroy');
    }

   public static function auth()
   {
       self::get('login', 'auth@index');
       self::get('register', 'auth@register');
       self::get('logout', 'auth@logout');
       self::post('process', 'auth@login');
   }

   /**
    * Raggruppa delle route con dei filtri uguali
    * @param array $filters
    * @param Closure $callback
    */
   public static function group(array $filters, \Closure $callback){
       $oldPrefix = self::$prefix;

       // Check prefisso di gruppo
       if(isset($filters['prefix'])){
           self::$prefix = self::addPrefix($filters['prefix']);
       }

       $callback();

       self::$prefix = $oldPrefix;
   }

   /**
    * Ritorna la lista delle route
    * @return array
    */
   public static function getRoutes(): array
   {
       return [self::$staticRoutes, self::$dynamicRoutes];
   }

   /**
    * Verifica la presenza di una $path
    * @param string $path
    * @return bool
    */
   public static function checkRoute(string $path): bool
   {
       if(isset(self::$staticRoutes[$path])){
           return true;
       }elseif(isset(self::$dynamicRoutes[$path])){
           return true;
       }

       return false;
   }

   
    /**
     * Metodi Http validi
     */
    public static function getValidMethods()
    {
        return [
            'ANY',
            'GET',
            'POST',
            'PUT',
            'PATCH',
            'DELETE',
            'HEAD',
            'OPTIONS',
        ];
    }

    public static function validateHttpRequestMethod($method) {
        if(empty($method)) {
            throw new InvalidArgumentException('I need valid value');
        }
        switch ($method) {
            case 'GET':
            case 'POST':
            case 'PUT':
            case 'DELETE':
            case 'HEAD':
                return $method;
                break;
            default:
                throw new InvalidArgumentException('Unexpected value.');
                break;
        }
    }

}