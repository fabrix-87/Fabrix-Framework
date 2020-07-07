<?php

namespace App\Mappers;

use System\Data\DataMapper;
use \PDO;
// use App\User;

/**
 *
 */
class user_Mapper extends DataMapper
{  
    /**
     * Get hashed password from DB
     * @param  string $username
     * @return array user_id, password
     */
    public function getPassword(string $username)
    {
        $sql = "SELECT $this->_primary, password  FROM $this->_table WHERE username = ?";
        $stmt = $this->_db->run($sql, [$username]);
        return $stmt->fetch();
    }

}



 ?>
