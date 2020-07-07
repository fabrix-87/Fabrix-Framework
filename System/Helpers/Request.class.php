<?php

namespace System\Helpers;

//use System\Core\Router;
use System\Helpers\Session;

/**
 * Request class
 * Gestisce tutte le richieste dal browser
 */
class Request
{
    // contiene l'uri della pagina
    private $uri;

    // array con i parametri GET
    private $getParams;
    // array con i parametri POST
    private $postParams;
    // la route da richiamare
    private $routePath;
    // Metodo Http
    private $httpMethod;

    // Metodo di output
    private $httpOutput = 'html';

    // Modalità (web|api)
    private $mode;

    public function __construct()
    {
        $this->uri = $_GET['url'] ?? '';
        unset($_GET['url']);

        $this->mode = $_GET['requestMode'] ?? 'web';
        unset($_GET['requestMode']);

        $this->httpOutput = $_GET['outputType'] ?? 'html';
        unset($_GET['outputType']);

        $this->parseUri();
        $this->parseGetData();
    }

               
    /**
     * getRequestMode
     *
     * @return string web|api
     */
    public function getRequestMode():string
    {
        return $this->mode;        
    }

    /**
     * getHttpOutput
     *
     * @return string html|json
     */
    public function getHttpOutput(): string
    {
        return $this->httpOutput;
    }

    /**
     * Controlla e salva i dati passati via GET nella variabile statica $getParams
     */
    private function parseGetData()
    {
        $this->getParams = $this->sanitize_xss($_GET);
    }

    /**
     * Controlla e salva i dati passati via Post nella variabile statica $postParams
     */
    public function parsePostData()
    {
        // verifica la presenza del token per la protezione CSRF
        if (empty($_POST)){
            $this->postParams = [];
        } elseif(isset($_POST['CSRF']) and Session::get('token')) {
            if (hash_equals(Session::get('token'), $_POST['CSRF']))
            {
                $this->postParams = $this->sanitize_xss($_POST);
            } else {
                throw new \Exception("Token CSRF non valido", 1);
            }
        }else{
            throw new \Exception("Token CSRF non trovato", 1);
        }

    }

    public function sanitize_xss($value) {
        if(is_array($value))
        {
            $sanitized = array();
            foreach($value as $k => $v){
                $sanitized[$k] = $this->sanitize_xss($v);
            }
            return $sanitized;
        }else{
            return htmlspecialchars(strip_tags($value));
        }

    }

    /**
     * Ritorna l'uri corrente
     * @return string $uri
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    public function getPath(): string
    {
        return $this->routePath;
    }

    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }

    private function parseUri()
    {
        // $this->routePath = str_replace(BASE_PATH, '', parse_url($this->uri, PHP_URL_PATH));
        $this->routePath = preg_replace('/^'.str_replace('/','\\/',BASE_PATH).'/', '', parse_url($this->uri, PHP_URL_PATH));

        $this->httpMethod = $_SERVER['REQUEST_METHOD'];
    }

    public function post(string $name)
    {
        return isset($this->postParams[$name]) ? $this->postParams[$name] : false;
    }

    public function get(string $name)
    {
        return isset($this->getParams[$name]) ? $this->getParams[$name] : false;
    }


}



 ?>
