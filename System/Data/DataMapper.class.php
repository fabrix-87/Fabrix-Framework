<?php

namespace System\Data;

use System\Database\DB;
use System\Data\DataModel;
use \PDO;
use \Exception;

/**
 *
 */
class DataMapper
{
    protected $_db;
    // name of the table
    protected $_table;

    // primary key
    protected $_primary;

    // table fields fillable
    protected $_fillable = [];

    // Model
    protected DataModel $_model;

        
    /**
     * _where - Contains values for where clause ex:[][field, operator, value]
     *
     * @var array
     */
    private $_where = [];
    private $_orWhere = [];
        
    /**
     * __construct
     *
     * @param  DB $db
     * @return void
     */
    public function __construct(DB $db, DataModel $model)
    {
        $this->_db = $db;
        $this->_model = $model;

        $this->_table = $model->getTableName();
        $this->_primary = $model->getPrimaryKey();
        $this->_fillable = $model->getFillableFields();

        if (!$this->_table and !$this->_primary) {
            throw new Exception("Table name or primary key are not defined");
        }
    }
            

    /**
     * Find a record by ID and store it into the model
     * @param  [type] $id
     * @return 
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM `$this->_table` WHERE `$this->_primary` = ?";
        $stmt = $this->_db->run($sql, [$id]);
        $stmt->setFetchMode(PDO::FETCH_INTO, $this->_model);
        return $stmt->fetch();
    }
    
    /**
     * Find a record by ID or throw an Error
     *
     * @param  mixed $id
     * @return void
     */
    public function findOrFail($id)
    {
        if($this->findById($id) === false){
            throw new Exception("Record with id = {$id} not found in the table {$this->_table}");
        }else{
            return true;
        }

    }
    
    
    /**
     * Contains values for where clause 
     * usage1: where(field, operator, value)
     * usage2: where(filed, value) implicit equals operator
     * usage3: 
     * where([
     *  [field1,operator1,value1],
     *  [field2,operator2,value2]
     * ]) // array1 AND array2 in sql query
     *
     * @param  mixed $condition
     * @param  mixed $operator
     * @param  mixed $value
     * @return self
     */
    public function where($condition, $operator = null, $value = null): self
    {
        $this->saveWhere($this->_where,$condition,$operator,$value);
        
        return $this;
    }

    /**
     * Contains values for where clause 
     * usage1: where(field, operator, value)
     * usage2: where(filed, value) implicit equals operator
     * usage3: 
     * where([
     *  [field1,operator1,value1],
     *  [field2,operator2,value2]
     * ]) // array1 OR array2 in sql query
     *
     * @param  mixed $condition
     * @param  mixed $operator
     * @param  mixed $value
     * @return self
     */
    public function orWhere($condition, $operator = null, $value = null): self
    {
        $this->saveWhere($this->_orWhere,$condition,$operator,$value);
        
        return $this;
    }

    private function saveWhere(&$andOr, $condition, $operator = null, $value = null)
    {
        if(is_array($condition))
        {
            $ele = count($condition)*3;
            $totalEle = count($condition,COUNT_RECURSIVE)-count($condition);

            if($ele === $totalEle)
            {
                $andOr = array_merge($andOr,$condition);
            }else{
                throw new Exception('Invalid Where conditions');
            }
            
        }else{
            if($operator === null)
            {
                $value = $operator;
                $operator = '=';
            }
            $andOr[] = [$condition,$operator,$value];
        }
        
        return $this;
    }
    
    /**
     * Convert the where arrays in a sql string
     *
     * @return array [sql_string, values[]]
     */
    private function stringifyWhere(): array
    {
        $where = '';
        $data = [];

        if(count($this->_where) > 0)
        {            
            $where = 'WHERE (';

            // AND
            foreach($this->_where as $condition)
            {                
                if(count($condition) < 3) continue;

                $where .= "`$condition[0]` $condition[1] ? AND ";
                $data[] = $condition[2];
            }
            $where = rtrim($where,'AND ').') OR ';

            // OR
            foreach($this->_orWhere as $condition)
            {                
                if(count($condition) < 3) continue;

                $where .= "`$condition[0]` $condition[1] ? OR ";
                $data[] = $condition[2];
            }
            $where = rtrim($where,'OR ');

            $this->_where = [];
            
        }        

        return [$where, $data];
    }

    
    /**
     * Return all records
     *
     * @param  mixed $limit
     * @param  mixed $limitOffset
     * @return array
     */
    public function findAll(int $limit = 0, int $limitOffset = 0): array
    {
        list($whereClause, $datas) = $this->stringifyWhere();

        $sql = "SELECT * FROM `$this->_table` $whereClause";
        if($limit > 0)
        {
            $sql .= " LIMIT {$limit} OFFSET {$limitOffset}";
        }

        //die($sql);

        $stmt = $this->_db->run($sql, $datas);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_class($this->_model));
        return $stmt->fetchAll();
    }

     
     /**
      * Store the current model in to the database 
      *
      * @param  mixed $data
      * @param  mixed $insertPk
      * @return void
      */
     public function store(bool $insertPk = false)
     {         
         $data = $this->_model->getParams();
         if (!$insertPk) unset($data[$this->_primary]);

         $dataNames = array_keys($data);
         if (array_diff($dataNames, $this->_fillable)) {
             throw new Exception("Disallowed field name in the insert data");
         }
         $fieldsStr = "`".implode("`, `", $dataNames)."`";
         $valuesStr = str_repeat('?,', count($data) - 1) . '?';
         $sql = "INSERT INTO {$this->_table} ({$fieldsStr}) VALUES ({$valuesStr})";
         $this->_db->run($sql, array_values($data));
    }

    
    /**
     * Delete a record
     *
     * @param  mixed $id
     * @return void
     */
    public function delete($id)
    {
        $this->findOrFail($id);
        $sql = "DELETE FROM {$this->_table} WHERE {$this->_primary} = {$id}";
        $this->_db->run($sql);
    }

    
    /**
     * Update the current model
     *
     * @param  mixed $data
     * @param  mixed $id
     * @return void
     */
    public function update()
     {
        $data = $this->_model->getParams();

        if(!isset($data[$this->_primary]))
        {
            throw new Exception('You must load the record before updating');
        }

        $id = $data[$this->_primary];
        unset($data[$this->_primary]);
        unset($data['created_at']);
        unset($data['updated_at']);

        $dataNames = array_keys($data);
        if (array_diff($dataNames, $this->_fillable)) {
            throw new Exception("Disallowed field name in the insert data");
        }
        $fieldsStr = "`".implode("`=?, `", $dataNames)."`=?";
        
        $sql = "UPDATE {$this->_table} SET {$fieldsStr} WHERE {$this->_primary}=?";
        
        array_push($data,$id);
        
        $this->_db->run($sql, array_values($data));
    }    

}




 ?>
