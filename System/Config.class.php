<?php
namespace System;

/**
 * Classe di registro di configurazione
 */
class Config {
    /**
     * Array con tutte le configurazioni
     * @var array
     */
    private $data = [];

    public function __construct()
    {
        $this->loadConfig();
    }

    /**
     * Scansiona la cartella di configurazione
     * e inserisce i dati di ogni file nel registro di configurazione
     */
    private function loadConfig()
    {
        $dir = scandir(CONFIG_PATH);
        foreach ($dir as $file) {
            if($file == '.' or $file == '..'){ continue; }

            $conf_info = pathinfo(CONFIG_PATH.$file);

            if($conf_info['extension'] == 'php'){
                $this->set($conf_info['filename'], include(CONFIG_PATH.$file));
            }
        }
    }

    public function get($key)
    {
        if(isset($this->data[$key]))
        {
            return $this->data[$key];
        }else{
            return null;
        }
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }
}
