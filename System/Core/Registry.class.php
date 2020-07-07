<?php
/**
 * Classe registro contenete tutte le dipendenze
 * Dependency Injection
 */
namespace System\Core;

class Registry {

    protected $registry = array();

    /**
     * Salva i settaggi nel registro
     * @param String $key l'indice per l'array
     * @param String $data
     * @return void
     */
    public function set($key, $data) {
	       $this->registry[$key] = $data;
    }

    /**
     * Ritrona un settaggio dal registro
     * @param String $key l'indice dell'array
     * @return Object
     */
    public function get($key) {
    	if (isset($this->registry[$key]))
    	    return $this->registry[$key];
    }
}
