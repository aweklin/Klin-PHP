<?php

namespace Framework\Core;

use \Exception;
use \stdClass;
use Framework\Core\Database;
use Framework\Interfaces\IDatabase;
use Framework\Infrastructure\{ErrorLogger};
use Framework\Utils\{Str, Ary, Date};
use Framework\Interfaces\ILogger;
use PDO;

/**
 * Encapsulates the logic to manage each table in the database.
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
class Model implements IDatabase {

    protected Database $database;
    
    protected ILogger $_logger;
    private string $_table;
    private string $_modelName;
    private bool $_isSoftDeleteEnabled = false;
    private array $_columnNames = [];
    private array $_parameters = [];
    private array $_bindable = [];
    private array $_order = [];
    private string $_limit = '';
    private array $_joins = [];
    private array $_relationships = [];
    protected $_idField = DEFAULT_PRIMARY_FIELD;

    protected string $_errorMessage = '';

    public PDO|null $pdo;
    public int $rowCount;

    public function __construct(string $tableName = '') {
        $this->_logger = new ErrorLogger();

        $this->database = Database::getInstance();
        if ($this->database->hasError())
            $this->_errorMessage = $this->database->getErrorMessage();
            
        $this->database->setIdField($this->_idField);

        $this->pdo = $this->database->pdo;

        $this->_clear();

        $this->setModel(get_class($this), $tableName);
        $this->_setTableColumns();
    }

    protected function setModel(string $modelName, string $tableName = '') {
        global $inflection;

        $modelNameArray     = explode(DS, $modelName);
        $modelName          = array_pop($modelNameArray);
        $this->_modelName   = $modelName;
        $this->_table       = ($tableName ? $tableName : $this->toTableName($this->_modelName));
    }

    protected function setTableName(string $tableName) {
        $this->_table = $tableName;
    }

    protected function toTableName($modelName) : string {
        if (DATABASE_TABLE_NAMES_PLURALIZED) {
            global $inflection;

            $modelName = $inflection->delimit($modelName);
            return $inflection->pluralize($modelName);
        } else {
            return $modelName;
        }
    }

    public function hasError() : bool {
        return (!Str::isEmpty($this->_errorMessage));
    }

    public function getErrorMessage() : string {
        return $this->_errorMessage;
    }

    public function getPrimaryField() : string {
        return $this->_idField;
    }

    public function setPrimaryField(string $idField) {
        $this->database->setIdField($idField);
    }

    private function _setTableColumns() {
        $columns = $this->_getColumns();//dnd($columns);
        if ($columns && count($columns) > 0) {
            foreach($columns as $column) {
                $columnName = $column->Field;
                array_push($this->_columnNames, $columnName);

                // check if this column is the primary key 
                if (Str::toLower($column->Key) === 'pri') {
                    $this->_idField = $columnName;
                    $this->database->setIdField($columnName);//echo 'Primary key: ' . $columnName . '<br>';
                    
                    // check if primary key is auto_increment
                    $extra = Str::toLower($column->Extra);
                }

                if ($column->Default) { 
                    // default value was specified on the table, use it
                    
                    $this->{$columnName} = $column->Default;

                } else {
                
                    // determine column default value base on column type
                    $columnType = Str::toLower($column->Type);

                    if (preg_match('/int/i', $columnType) || 
                        preg_match('/bigint/i', $columnType) || 
                        preg_match('/smallint/i', $columnType) || 
                        preg_match('/mediumint/i', $columnType) || 
                        preg_match('/float/i', $columnType) || 
                        preg_match('/double/i', $columnType) || 
                        preg_match('/decimal/i', $columnType) || 
                        preg_match('/year/i', $columnType) || 
                        preg_match('/tinyint/i', $columnType) || 
                        preg_match('/bit/i', $columnType)) {
                        
                        $this->{$columnName} = 0;

                    } elseif (preg_match('/char/i', $columnType) || 
                        preg_match('/varchar/i', $columnType) || 
                        preg_match('/text/i', $columnType) || 
                        preg_match('/json/i', $columnType) || 
                        preg_match('/nchar/i', $columnType) || 
                        preg_match('/nvarch/i', $columnType) || 
                        preg_match('/longtext/i', $columnType) || 
                        preg_match('/mediumtext/i', $columnType) || 
                        preg_match('/tinytext/i', $columnType) || 
                        preg_match('/date/i', $columnType) || 
                        preg_match('/datetime/i', $columnType) || 
                        preg_match('/timestamp/i', $columnType)) {

                        $this->{$columnName} = '';

                    } else {

                        $this->{$columnName} = null;

                    }
                }

                // check if soft delete is enabled on the table
                if ($columnName == DEFAULT_FIELD_DELETED) {
                    $this->_isSoftDeleteEnabled = true;
                }
            }
            //dnd($this);
        }
    }

    private function _getColumns() {
        return $this->database->getColumns($this->_table);
    }

    public function where(string $field, mixed $operatorOrValue, mixed $value = null) : Model {
        if ($value === null) {
            if (in_array(Str::toLower($operatorOrValue), ['is not', 'is not null'])) {
                array_push($this->_parameters, "{$field} {$operatorOrValue}");
                return $this;    
            }

            array_push($this->_parameters, "{$field} = ?");
            array_push($this->_bindable, $operatorOrValue);

            return $this;
        }

        array_push($this->_parameters, "{$field} {$operatorOrValue} ?");
        array_push($this->_bindable, $value);
        
        return $this;
    }

    public function whereEquals(string $field, mixed $value) : Model {
        return $this->where($field, '=', $value);
    }

    public function whereNotEquals(string $field, mixed $value) : Model {
        return $this->where($field, '!=', $value);
    }

    public function whereGreaterThan(string $field, mixed $value) : Model {
        return $this->where($field, '>', $value);
    }

    public function whereGreaterOrEquals(string $field, mixed $value) : Model {
        return $this->where($field, '>=', $value);
    }

    public function whereLessThan(string $field, mixed $value) : Model {
        return $this->where($field, '<', $value);
    }

    public function whereLessOrEquals(string $field, mixed $value) : Model {
        return $this->where($field, '<=', $value);
    }

    public function whereBetween(string $field, mixed $lowerBound, mixed $upperBound) : Model {
        return $this->whereGreaterOrEquals($field, $lowerBound)
            ->_and()
            ->whereLessOrEquals($field, $upperBound);
    }

    public function whereNull(string $field) : Model {
        return $this->where($field, 'Is Null');
    }

    public function whereNotNull(string $field) : Model {
        return $this->where($field, 'Is Not Null');
    }

    public function withOne(string $foreignTableName, string $foreignFieldName = '', string $primaryFieldName = 'id') : Model {
        if(Str::isEmpty($foreignTableName)) throw new Exception("Foreign field table name is required for {$this->_table} in order to use the withOne method.");
        if(Str::isEmpty($foreignFieldName)) 
            $foreignFieldName = "{$foreignTableName}_id";

        return $this->addRelationship(Database::RELATIONSHIP_CHILD, $this->_table, $foreignFieldName, $foreignTableName, $primaryFieldName);
    }

    public function withMany(string $foreignTableName, string $foreignFieldName = '', string $primaryFieldName = 'id') : Model {
        if(Str::isEmpty($foreignTableName)) throw new Exception("Foreign field table name is required for {$this->_table} in order to use the withMany method.");
        if(Str::isEmpty($foreignFieldName)) 
            $foreignFieldName = "{$foreignTableName}_id";

        return $this->addRelationship(Database::RELATIONSHIP_CHILDREN, $this->_table, $primaryFieldName, $foreignTableName, $foreignFieldName);
    }

    public function join(string $clause) : Model {
        array_push($this->_joins, $clause);

        return $this;
    }

    /**
     * Adds a new relationship to the select result.
     * 
     * @param string $type Specifies relationship type. Must be one of the following: child (i.e: one to one), children (i.e one to many).
     * @param string $primaryTableName Specifies the primary table name. If empty string or null is passes, the current model's table name is used.
     * @param string $primaryFieldName Specifies the primary field name. If empty string or null is passes, the current model's field name is used.
     * @param string $foreignTableName Specifies the foreign table name.
     * @param string $foreignFieldName Specified the foreign field name.
     * 
     * @return Model
     */
    public function addRelationship(
        string $type, 
        string $primaryTableName = '', 
        string $primaryFieldName = '', 
        string $foreignTableName = '', 
        string $foreignFieldName = '', 
        string $orderBy = '') : Model {
        if (Str::isEmpty($primaryTableName)) $primaryTableName = $this->_table;
        if (Str::isEmpty($primaryFieldName)) $primaryFieldName = $this->_idField;

        $this->_relationships[Str::toLower($type)][] = [
            'primaryTable'  => Str::toLower($primaryTableName),
            'primaryField'  => Str::toLower($primaryFieldName),
            'foreignTable'  => Str::toLower($foreignTableName),
            'foreignField'  => Str::toLower($foreignFieldName),
            'order'         => Str::toLower($orderBy)
        ];

        return $this;
    }

    public function getLastInsertId() : int {
        return $this->database->getLastInsertId();
    }

    public function _and() : Model {
        array_push($this->_parameters, " AND ");

        return $this;
    }

    public function _or() : Model {
        array_push($this->_parameters, " OR ");

        return $this;
    }

    public function orderBy(string $field, string $direction = 'ASC') : Model {
        array_push($this->_order, "{$field} {$direction}");

        return $this;
    }

    public function limit(int $limit) : Model {
        $this->_limit = $limit;

        return $this;
    }

    private function _composeQueryParts(bool $excludeDeleted = true) : array {
        $parameters = [];

        if ($this->_order) {
            $order = '';
            foreach($this->_order as $item) {
                $order .= ' ' . $item . ',';
            }
            $order = rtrim(trim($order), ',');

            $parameters['order'] = $order;
        }
        if ($this->_limit) {
            $parameters['limit'] = $this->_limit;
        }
        if ($this->_bindable) {
            $parameters['bindable'] = $this->_bindable;
        }
        $conditionsParameter = 'conditions';
        if ($this->_parameters) {
            $parameters[$conditionsParameter] = $this->_parameters;
        }
        if ($this->_joins) {
            $parameters['joins'] = $this->_joins;
        }
        if ($this->_relationships) {
            $parameters['relationships'] = $this->_relationships;
        }
        if ($excludeDeleted && $this->_isSoftDeleteEnabled) {
            $deletedClause =  "`" . DEFAULT_FIELD_DELETED . "` != 1";            
            if (array_key_exists($conditionsParameter, $parameters)) {
                if (is_array($parameters[$conditionsParameter])) {
                    if (!in_array(DEFAULT_FIELD_DELETED, $parameters)) {
                        array_push($parameters[$conditionsParameter], " AND " . $deletedClause);
                    } else {
                        array_push($parameters[$conditionsParameter],  $deletedClause);
                    }
                } else {
                    $parameters[$conditionsParameter] .= " AND " . $deletedClause;
                }
            } else {
                $parameters[$conditionsParameter] = $deletedClause;
            }
        }

        return $parameters;
    }

    public function select() {
        $results = null;

        $parameters = $this->_composeQueryParts();

        $queryResult = $this->database->select($this->_table, $parameters);
        $this->_errorMessage = $this->database->getErrorMessage();
        $this->rowCount = $this->database->rowCount;
        if (!$this->hasError() && $queryResult) {
            $results = [];
            foreach($queryResult as $result) {
                $model = (IS_DEVELOPMENT ? 'App\Src\Models\\' : '') . $this->_modelName;
                $object = new $model();
                $object->_set($result);
                array_push($results, $this->getData($object));
            }
        }

        $this->_clear();

        return $results;
    }

    public function fetch() : array {
        $result = $this->database->fetch($this->_table, $this->_composeQueryParts());
        $this->_clear();
        return $result;
    }

    public function executeStoredProcedure(string $procedureName, array $parameters = [], bool $isSelectingRecords = true, string $objectOutputName = 'procedure_results') {
        return $this->database->executeStoredProcedure($procedureName, $parameters, $isSelectingRecords, $objectOutputName);
    }

    public function find() {
        $parameters = $this->_composeQueryParts(false);

        $result = $this->database->find($this->_table, $parameters);

        $this->_clear();

        return ($result) ? $this->getData($result) : null;
    }

    public function findObject() {
        $parameters = $this->_composeQueryParts(false);

        $result = $this->database->find($this->_table, $parameters);

        $this->_clear();

        $model = (IS_DEVELOPMENT ? 'App\Src\Models\\' : '') . $this->_modelName;

        $object = new $model();
        if ($result) {
            foreach($result as $key => $value) {
                $object->$key = $value;
            }
        }

        return $object;
    }

    private function _set($result) {
        foreach($result as $key => $value) {
            $this->$key = $value;
        }
    }

    public function findById($value) {
        return $this->where("`{$this->_idField}`", '=', $value)->find();
    }

    public function save(array $fields = []) {
        $isInserting = true;

        // confirm if $fields array was passed as argument

        if ($fields && count($fields) > 0) {

            // confirms if the primary field is set
            if (array_key_exists($this->_idField, $fields)) {

                $isInserting = false;
                
            }

        } else {

            // no fields argument was passed, use the table fields to carry out the operation
            $fields = [];

            foreach($this->_columnNames as $columnName) {
                if (!$this->{$columnName}) continue;    // only add fields specified
                $fields[$columnName] = $this->$columnName;
            }

            if (property_exists($this, $this->_idField) && $this->{$this->_idField}) {
                $isInserting = false;
            }
        }

        // set default field values
        if ($isInserting) {

            // check for default fields and set their values
            if (!array_key_exists(DEFAULT_FIELD_CREATED, $fields) && property_exists($this, DEFAULT_FIELD_CREATED)) {
                $fields[DEFAULT_FIELD_CREATED] = Date::now();
            }
            if (!array_key_exists(DEFAULT_FIELD_MODIFIED, $fields) && property_exists($this, DEFAULT_FIELD_MODIFIED)) {
                $fields[DEFAULT_FIELD_MODIFIED] = Date::now();
            }
            // if (!array_key_exists(DEFAULT_FIELD_DELETED, $fields) && property_exists($this, DEFAULT_FIELD_DELETED)) {
            //     $fields[DEFAULT_FIELD_DELETED] = 0;
            // }
        } else {
            
            // check for default fields and set their values
            if (!array_key_exists(DEFAULT_FIELD_MODIFIED, $fields) && property_exists($this, DEFAULT_FIELD_MODIFIED)) {
                $fields[DEFAULT_FIELD_MODIFIED] = Date::now();
            }
        }

        //echo ($isInserting ? 'Inserting...' : 'Updating...');
        //dnd($fields);
        if ($isInserting) {
            // proceed to insert
            $this->database->insert($this->_table, $fields);
        } else {
            // proceed to update
            $this->database->update($this->_table, $fields[$this->_idField], $fields);
        }

        $this->_errorMessage = $this->database->getErrorMessage();
        $this->rowCount = $this->database->rowCount;
    }

    public function delete($idValue, bool $forceDelete = false) {
        if (!$idValue &&
            (property_exists($this, $this->_idField) && $this->{$this->_idField}
            && !$this->_parameters)) {
            $idValue = $this->{$this->_idField};
        }
        if (!$idValue && !$this->_parameters) {
            $this->_errorMessage = 'Please specify the primary field value or set of parameters to use for this delete operation.';
        } else {
            if ($idValue) {
                if ($this->_isSoftDeleteEnabled && !$forceDelete) {
                    $this->save([$this->_idField => $idValue, DEFAULT_FIELD_DELETED => 1]);
                } else {
                    $this->database->delete($this->_table, $idValue);
                    $this->_errorMessage = $this->database->getErrorMessage();
                    $this->rowCount = $this->database->rowCount;
                }
            } else {
                $whereClause = '';
                foreach($this->_parameters as $parameter) {
                    $whereClause .= ' ' . $parameter;
                }
                $whereClause = trim($whereClause);
                $sql = "DELETE FROM `{$this->_table}` WHERE {$whereClause}";//dnd($sql);
                $this->query($sql, $this->_bindable);
            }
        }
        $this->_clear();
    }

    public function startTransaction() {
        $this->database->startTransaction();
    }

    public function commitTransaction() {
        $this->database->commitTransaction();
    }
    
    public function rollbackTransaction() {
        $this->database->rollbackTransaction();
    }

    public function query(string $sql, array $parameters = []) {
        $queryResult = $this->database->query($sql, $parameters);
        $this->_errorMessage = $this->database->getErrorMessage();
        $this->rowCount = $this->database->rowCount;

        $this->_clear();

        return $queryResult;
    }

    public function queryWithResult(string $sql, array $parameters = []) {
        $queryResult = $this->database->queryWithResult($sql, $parameters);
        $this->_errorMessage = $this->database->getErrorMessage();
        $this->rowCount = $this->database->rowCount;

        $this->_clear();

        return $queryResult;
    }

    public function queryWithResultAsArray(string $sql, array $parameters = []) : array {
        $queryResult = $this->database->queryWithResultAsArray($sql, $parameters);
        $this->_errorMessage = $this->database->getErrorMessage();
        $this->rowCount = $this->database->rowCount;

        $this->_clear();

        return $queryResult;
    }

    public function count() : int {
        $parameters = $this->_composeQueryParts();
        $fieldToCount = (isset($this->{$this->_idField}) ? '`' . $this->_idField . '`' : '*');
        $result = $this->database->count($this->_table, $fieldToCount, $parameters);

        $this->_clear();

        return (int) $result;
    }

    public function getData($result) {
        if (!$result) return null;

        $data = new stdClass();

        foreach($result as $key => $value) {
            if (in_array($key, $this->_columnNames)) {
                $data->$key = $value;
                $this->$key = $value;
            }
        }

        return $data;
    }

    public function getDataFromResult($item) {
        if (!$item) return null;

        $result = $this->database->getResult();

        $data = new stdClass();
        
        foreach($result as $key => $value) {
            $data->$key = $value;
        }

        return $data;
    }

    public function getSingleDataFromResult($item) {
        $data = $this->getDataFromResult($item);
        if (!$data) return null;
        $ary = Ary::convertFromObject($data);
        return (count($ary) > 1 ? $ary[0] : $ary);
    }

    public function set(array $input) : Model {
        if (!Ary::isAssociative($input)) 
            throw new Exception("Invalid input for model: {$this->_modelName}");
        
        foreach($input as $key => $value) {
            if (in_array($key, $this->_columnNames)) {  //TODO:: may remove this later
                $this->{$key} = $value;
            }
        }
        
        return $this; 
    }

    protected function _clear() {
        $this->_parameters = [];
        $this->_bindable = [];
        $this->_order = [];
        $this->_joins = [];
        $this->_relationships = [];
        $this->_limit = '';        
    }

}