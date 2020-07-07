<?php

namespace System\Core;

use System\Helpers\{Session, Alerts};
use System\Core\Registry;
use System\Routing\Routes;
use Twig\{TwigFunction, Environment, Markup };
use Twig\Loader\FilesystemLoader;

require_once("vendor/autoload.php");

/**
*
* @version 0.5a
* @autor Menza Fabrizio
*
* */
class View {

    private $registry;

    private $template;
    private $keys = array();

    private $lang = array();
    private $twig;

    public function __construct(Registry $registry) {
        $this->registry = $registry;

        $loader = new FilesystemLoader('Templates');
        $this->twig = new Environment($loader);

        // crea la lista dei breadcrumb
        $this->twig->addFunction(new TwigFunction('breadcrumb', function ($breadcrumb = array()) {
            return  new Markup( View::createBreadcrumb($breadcrumb), 'UTF-8' );
        }));

        // crea l'url per i css
        $this->twig->addFunction(new TwigFunction('css', function ($asset) {
            return sprintf(BASE_PATH.'assets/css/%s', ltrim($asset, '/'));
        }));

        // crea l'url per i js
        $this->twig->addFunction(new TwigFunction('js', function ($asset) {
            return sprintf(BASE_PATH.'assets/js/%s', ltrim($asset, '/'));
        }));

        // crea l'url per gli assets generici
        $this->twig->addFunction(new TwigFunction('assets', function ($asset) {
            return sprintf(BASE_PATH.'assets/%s', ltrim($asset, '/'));
        }));

        // crea l'url per le immagini
        $this->twig->addFunction(new TwigFunction('images', function ($asset) {
            return sprintf(BASE_PATH.'assets/img/%s', ltrim($asset, '/'));
        }));

        // genera il token CSRF
        $this->twig->addFunction(
            new TwigFunction(
                'CSRF',
                function($lock_to = null) {
                    $token = null;
                    //genera il token per la protezione CSRF
                    if (!(Session::get('token'))) {
                        Session::set('token', bin2hex(random_bytes(32)));
                    }
                    if (!(Session::get('token2'))) {
                        Session::set('token2', bin2hex(random_bytes(32)));
                    }
                    if (empty($lock_to)) {
                        $token = Session::get('token');
                    }else{
                        $token = hash_hmac('sha256', $lock_to, Session::get('token2'));
                    }

                    return new Markup('<input type="hidden" name="CSRF" value="'.$token.'">', 'UTF-8');

                }
            )
        );

        /**
         * Crea un url dai parametri passati
         * @param route
         * @param action
         * @param extra_field
         */
        $this->twig->addFunction(new TwigFunction('url', function($route = 'home@index',  $extra=array(),$backend = false) {
            if($backend === true){
                $route = BACKEND_PREFIX.'.'.$route;
            }
            return Routes::route($route, $extra);
        }));
    }

    //  aggiungi + leggi + aggiungi in una key esistente
    private function addKey($key, $value) {
        $this->keys[$key] = $value;
    }

    private function getKeys() {
        return $this->keys;
    }

    private function appendKey($key, $value) {
        $this->keys[$key] = $this->keys[$key] . $value;
    }


    /**
    * Unica funzione publica oltre il costruttore
    * Genera il template, richiama la view e i file di linguaggio e genera l'output
    */
    function show($file_name, $data = array(), $template = 'template') {
        $lang = array();

        $this->keys = array_merge($this->keys, $data);

        // if ($this->ajax)
        //     $this->buildTemplate('ajax');
        // else
        //     $this->buildTemplate($template);

        $this->storeDefaultKeys();

        $this->html = $this->twig->render($file_name.'.twig', $this->keys);

        //-- stampo l'output
        echo $this->html;
    }


    private function storeDefaultKeys() {
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $this->addKey("base_path", $protocol.$_SERVER['SERVER_NAME'].BASE_PATH);
        $this->addKey("alerts", Alerts::gets());
        $this->addKey("site_title", '');
        $this->addKey("page_title", '');
        $this->addKey("custom_header", '');
        $this->addKey("extrabox", '');
        $this->addKey("box-left", '');
        $this->addKey("_user", $this->registry->get('user')->getUserData());
    }


    public static function createBreadcrumb($crumbs = array()){
        $crumbs = array('Home' => Routes::route(Routes::currentPrefix().'home@index')) + $crumbs;
        $last = array_key_last($crumbs);

        $breadcrumb = '<ol class="breadcrumb float-right">';
        foreach($crumbs as $title=>$href){
            if($title === $last){
                $breadcrumb .= '<li class="breadcrumb-item active">'.$title.'</li>';
            } else {
                $breadcrumb .= '<li class="breadcrumb-item"><a href="'.$href.'">'.$title.'</a></li>';
            }
        }

        $breadcrumb .= '</ol>';

        return $breadcrumb;
    }

}
