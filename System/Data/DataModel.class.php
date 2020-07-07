<?php

namespace System\Data;

use Exception;
use JsonSerializable;
use System\Helpers\Validation;

abstract class DataModel implements JsonSerializable 
{    
    // name of the table
    protected $table;

    // primary key
    protected $primary = 'id';

    // table fields fillable
    protected $fillable = [];

    // content param ['fieldName' => 'value']
    protected $params = [];

    // don't return these params when getAll be called
    protected $hidden = [];


    protected $validation = [];

    private $is_valid = true;

    private $errors = [];
    

    /**
     * __set - setta un parametro nell'array params
     *
     * @param  mixed $key
     * @param  mixed $value
     * @return void|mixed
     */
    public function __set($key, $value)
    {
        if(property_exists($this, 'params'))
            $this->params[$key] = $value;
    }
    
    /**
     * __get - ritorna un parametro 
     *
     * @param  mixed $key
     * @return void|mixed
     */
    public function __get($key)
    {
        if(property_exists($this,'params') and array_key_exists($key, $this->params)){
            return $this->params[$key];
        }
    }
    
    /**
     * getAll - return all params
     *
     * @return void|array
     */
    public function getAll()
    {
        if(property_exists($this,'params')){
            return array_diff_key($this->params, array_flip($this->hidden));
        }        
    }
        
    /**
     * Ritorna il nome della tabella del DB
     *
     * @return string
     */
    public function getTableName(): string
    {
        return $this->table;
    }
    
    /**
     * Ritorna il nome della chiave primaria nella tabella
     *
     * @return string
     */
    public function getPrimaryKey(): string
    {
        return $this->primary;
    }
    
    /**
     * Ritorna i campi compilabili
     *
     * @return array
     */
    public function getFillableFields(): array
    {
        return $this->fillable;
    }
    
    /**
     * Ritorna tutti i parametri 
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    
    /**
     * Inserisce i valori presenti nell'array nei parametri dell'oggetto
     *
     * @param  array $data
     * @return void|Exception
     */
    public function fillAll(array $data)
    {
        $dataNames = array_keys($data);
        if (array_diff($dataNames, $this->fillable)) {
            throw new Exception("Disallowed field name in the insert data");
        }

        $this->params = array_replace($this->params, $data);    
    }
    
    /**
     * Inserisce un valore 
     *
     * @param  mixed $field
     * @param  mixed $value
     * @return void
     */
    public function fill($field, $value)
    {
        if(in_array($field, $this->fillable)){
            $this->params[$field] = $value;
        }
    }


    
    /**
     * Valida i dati inseriti se le regole sono settate su $validation
     *
     * @return bool
     */
    public function validate()
    {
        if(empty($this->validation)) return true;

        $validator = new Validation();
        foreach($this->validation as $field => $stringRules)
        {
            $validator->name($field)->value($this->params[$field] ?? '');

            $rules = explode('|',$stringRules);
            foreach($rules as $rule)
            {
                if(strpos($rule,':'))
                {
                    list($rule,$value) = explode(':',$rule);
                }
                
                if(method_exists($validator,$rule) || array_key_exists($rule,$validator->patterns)){
                    switch($rule){
                        case 'required':
                            $validator->$rule();
                        break;
                        case 'min':
                        case 'max':
                        case 'equal':
                        case 'maxSize':
                        case 'ext':
                            if(!isset($value))
                                throw new Exception('Parametro richiesto per la regola: '.$rule);
                            $validator->$rule($value);
                        break;
                        default:
                            $validator->pattern($rule);
                    }
                }
            }            
        }
        $this->errors = $validator->getErrors();

        return $this->is_valid = $validator->isSuccess();        
    }

        
    /**
     * Verifica se i dati sono validi
     *
     * @return bool
     */
    public function is_valid(): bool
    {
        return $this->is_valid;
    }
    
    /**
     * Ritorna gli errori dei dati non validi
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function jsonSerialize() {
        return $this->getAll();
    }

}



?>
