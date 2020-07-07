<?php

namespace System\Database;

use \PDO;
use PDOException;

/**
 * Database connection class
 */
class DB
{
    protected $pdo;

    /**
     * Connect to database
     * @param array $config
     */
    public function __construct(array $dbConfig)
    {
        $connStr = 'mysql:host='.$dbConfig['server'].';port='.$dbConfig['port'].';dbname='.$dbConfig['name'].';charset='.$dbConfig['charset'];

        try{
            $this->pdo = new PDO(
                $connStr,
                $dbConfig['username'],
                $dbConfig['password']
            );
            # We can now log any exceptions on Fatal error.
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			# Disable emulation of prepared statements, use REAL prepared statements instead.
			$this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

            return true;

        // Error handling
        }catch(PDOException $e){
            throw new \Exception("Failed to connect to DB: ". $e->getMessage(), 1);
        }

    }

    /**
     * Execute a $sql query with or without params.
     * @param  string $sql    Query to execute
     * @param  array  $params Params
     * @return PDOStatement
     */
    public function run(string $sql, array $params = [])
    {
        if(empty($params)){
            return $this->pdo->query($sql);
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Returns the last inserted id
     * @return int ID
     */
    public function lastInsertId(): int
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Get the connection
     * @return PDO
     */
    public function getConnection()
    {
        return $this->pdo;
    }

    /**
     * Close the connection
     */
    public function closeConnection()
    {
        $this->pdo = null;
    }

}
 ?>
