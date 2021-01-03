<?php

namespace System\Http;

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

    // check per presenza di dati POST
    private $hasPost = false;

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
        $this->parsePostData();
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
    private function parsePostData()
    {
        // verifica la presenza del token per la protezione CSRF
        if (empty($_POST)){
            $this->postParams = [];
        } else {
            $this->postParams = $this->sanitize_xss($_POST);
            $this->isPost = true;
        }
    }
    
    /**
     * Controllo se sono presenti dati con metodo POST
     *
     * @return bool
     */
    public function hasPost(): bool
    {
        return $this->hasPost;
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
        return $this->postParams[$name] ?? false;
    }

    public function get(string $name)
    {
        return $this->getParams[$name] ?? false;
    }


}



 ?>
