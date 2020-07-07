<?php
/**
 *
 * @version 1.0
 * @autor Menza Fabrizio
 *
 **/

namespace System\Core;

use System\Core\Registry;
use System\Database\Database;

class Model{
        protected $db;
        protected $config;
        protected $registry;

        public function __construct(Registry $registry)
        {
            $this->registry = $registry;
            $this->db = $registry->get('db');
            $this->config = $registry->get('config');
        }


}


?>
