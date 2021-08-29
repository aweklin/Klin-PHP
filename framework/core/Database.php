<?php

namespace Framework\Core;

use \PDO;
use \PDOException;
use Framework\Interfaces\IDatabase;
use Framework\Utils\{Str, Ary};
use Framework\Decorator\Logger;
use Framework\Infrastructure\ErrorLogger;

/**
 * Encapsulates the various database operations on MySQL server
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */

class Database implements IDatabase {

    private static $instance;

    private $_errorMessage = '';
    private $_lastInsertId = 0;
    private $_idField = 'id';
    private $_query;
    private $_result;
    private $_logger;
    private $_data = [];
    private $_operators = [];

    public $pdo;
    public $rowCount;

    const RELATIONSHIP_CHILD = 'child';
    const RELATIONSHIP_GRAND_CHILD = 'grandchild';
    const RELATIONSHIP_CHILDREN = 'children';
    const RELATIONSHIP_GRAND_CHILDREN = 'grandchildren';
    const RELATIONSHIP_PRIMARY_TABLE = 'primaryTable';
    const RELATIONSHIP_PRIMARY_FIELD = 'primaryField';
    const RELATIONSHIP_FOREIGN_TABLE = 'foreignTable';
    const RELATIONSHIP_FOREIGN_FIELD = 'foreignField';

    const AND = 'AND';
    const OR = 'OR';
    const EQUALS = '=';
    const NOT_EQUALS = '!=';
    const LESS_THAN = '<';
    const LESS_OR_EQUALS = '<=';
    const GRATER_THAN = '>';
    const GRATER_OR_EQUALS = '>=';
    const LIKE = 'LIKE';
    const NOT_LIKE = 'NOT LIKE';
    const IN = 'IN';
    const NO_IN = 'NOT IN';
    const IS_NULL = 'IS NULL';
    const IS_NOT_NULL = 'IS NOT NULL';

    private const CANNOT_CONNECT = 'Unable to establish database connection.';

    private function __construct() {
        $this->_logger = new ErrorLogger();
        $this->_operators = [Database::EQUALS, Database::NOT_EQUALS, Database::LESS_THAN, Database::LESS_OR_EQUALS, 
            Database::GRATER_THAN, Database::GRATER_OR_EQUALS, Database::LIKE, Database::NOT_LIKE, Database::IN, 
            Database::NO_IN, Database::IS_NULL, Database::IS_NOT_NULL, Database::AND, Database::OR];
        try {
            $this->pdo = new PDO('mysql:host=' . DATABASE_HOST . '; dbname=' . DATABASE_NAME, 
                                DATABASE_USER, 
                                DATABASE_PASSWORD, 
                                [
                                    //PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION,
                                    PDO::ATTR_EMULATE_PREPARES, false
                                ]);
        } catch (PDOException $e) {
            $this->_errorMessage = (!IS_DEVELOPMENT ? USER_FRIENDLY_ERROR_MESSAGE : $e->getMessage());
            $this->_logger->log($e->getMessage());
        }
    }

    public static function getInstance() : Database {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function setIdField(string $fieldName): Database {
        $this->_idField = $fieldName;
        return $this;
    }

    public function hasError() : bool {
        return ($this->_errorMessage ? true : false);
    }

    public function getErrorMessage() : string {
        return $this->_errorMessage;
    }

    public function getLastInsertId() : int {
        return $this->_lastInsertId;
    }

    public function getResult() {
        return $this->_result;
    }

    public function first() {
        if (empty($this->_result)) return null;
        return $this->_result[0];
    }

    private function _containsOperator(string $str): bool {
        foreach($this->_operators as $item) {
            if (Str::contains(Str::toLower($str), Str::toLower($item))) return true;
        }
        return false;
    }

    /**
     * Removes any app generated namespace from the query to be executed, and returns a clean SQL statement.
     * 
     * @param string $sql The SQL statement to be sanitized.
     * 
     * @return string
     */
    private function _getCleanSQL($sql) : string {
        if (!$sql) return '';

        return trim(str_replace('app\\src\\models\\', '', $sql));
    }

    /**
     * Returns list of the columns in a given table.
     * 
     * @param string $table Specifies the table name you want to retrieve all the columns
     */
    public function getColumns(string $table) {
        if (!$table) {
            $this->_errorMessage = 'Table name is required.';
            return null;
        }

        $this->query("SHOW COLUMNS FROM `{$table}`");
        if ($this->hasError()) return null;

        return $this->_result;
    } 

    /**
     * Executes the given SQL statement against the underlying database. Please call the hasError() & getErrorMessage() methods for error details.
     * 
     * @param string $sql The SQL statement to execute. This can also be a stored procedure, DML or DDL statement.
     * @param array $parameters Values of the parameters used in your $sql variable.
     * 
     * @return Database
     */
    public function query(string $sql, array $parameters = []) : Database {
        $this->rowCount = 0;
        $this->_errorMessage = '';
        $this->_result = null;

        if (!$this->pdo) {
            $this->_errorMessage = self::CANNOT_CONNECT;
            return $this;
        }

        if (!$sql) {
            $this->_errorMessage = 'SQL statement is required.';
            return $this;
        }
        $sql = $this->_getCleanSQL($sql);
        $sqlLowerCase = Str::toLower($sql);
        if (!Str::startsWith($sqlLowerCase, 'insert') &&
            !Str::startsWith($sqlLowerCase, 'update') &&
            !Str::startsWith($sqlLowerCase, 'delete') &&
            !Str::startsWith($sqlLowerCase, 'call') &&
            !Str::startsWith($sqlLowerCase, 'select') &&
            !Str::startsWith($sqlLowerCase, 'show')) {
            $this->_errorMessage = 'Invalid SQL statement';
            return $this;
        }

        $this->_errorMessage = '';
        try {
            if ($this->_query = $this->pdo->prepare($sql)) {
                $parameterId = 1;

                if ($parameters && count($parameters) > 0) {
                    foreach($parameters as $parameter) {
                        if (is_array($parameter)) continue;
                        if (Str::contains(Str::toLower($parameter), 'join')) continue;
                        $this->_query->bindValue($parameterId, $parameter);
                        $parameterId++;
                    }
                }

                if ($this->_query->execute()) {                    
                    $this->_result      = $this->_query->fetchALL(PDO::FETCH_OBJ);
                    $this->rowCount     = $this->_query->rowCount();
                    $this->_lastInsertId= $this->pdo->lastInsertId();
                } else {
                    $errorInfo = print_r($this->_query->errorInfo()['2'], true);
                    $this->_errorMessage = (IS_DEVELOPMENT ? 'Error executing query: ' . PHP_EOL . $sql . PHP_EOL . 'Reason: ' . $errorInfo : USER_FRIENDLY_ERROR_MESSAGE);
                    if (!IS_DEVELOPMENT) {
                        $this->_logger->log('Error executing ' . PHP_EOL . $sql . PHO_EOL . 'Reason: ' . $errorInfo);
                    }
                }
            }
        } catch (PDOException $e) {
            $this->_errorMessage = (!IS_DEVELOPMENT ? USER_FRIENDLY_ERROR_MESSAGE : $e->getMessage());
            $this->_logger->log($e->getMessage());
        }

        return $this;
    }
    
    /**
     * Executes the given query and return the result of the query executed
     * 
     * @param string $sql The SQL statement to execute. This can also be a stored procedure, DML or DDL statement.
     * @param array $parameters Values of the parameters used in your $sql variable.
     */
    public function queryWithResult(string $sql, array $parameters = []) {
        $this->query($sql, $parameters);
        return $this->_result;
    }

    /**
     * Executes the given query and return the result of the query executed as an array.
     * 
     * @param string $sql The SQL statement to execute. This can also be a stored procedure, DML or DDL statement.
     * @param array $parameters Values of the parameters used in your $sql variable.
     */
    public function queryWithResultAsArray(string $sql, array $parameters = []) : array {
        $this->query($sql, $parameters);
        if ($this->_result) {
            return Ary::convertFromObject($this->_result);
        } else {
            return [];
        }
    }

    public function executeStoredProcedure(string $procedureName, array $parameters = [], bool $isSelectingRecords = true, string $objectOutputName = 'procedure_results') {        
        $this->rowCount = 0;
        $this->_errorMessage = '';
        
        if (!$this->pdo) {
            $this->_errorMessage = self::CANNOT_CONNECT;
            if (!IS_DEVELOPMENT) {
                $this->_logger->log($this->_errorMessage);
            }
            return null;
        }

        global $inflection;

        try {
            $this->_data = [];

            if (!Str::startsWith(Str::toLower(trim($procedureName)), 'call')) {
                $procedureName = 'CALL ' . trim($procedureName);
            }
            $procedureName = $this->_getCleanSQL($procedureName);

            if ($this->_query = $this->pdo->prepare($procedureName)) {
                if ($parameters && count($parameters) > 0) {
                    $parameterId = 1;
                    foreach($parameters as $parameter) {
                        if (is_array($parameter)) {
                            foreach($parameter as $param) {
                                if (\is_array($param)) continue;
                                if (!\is_numeric($param) && (Str::contains(Str::toLower($param), 'join') || $this->_containsOperator($param))) continue;
                                $this->_query->bindValue($parameterId, $param);
                                $parameterId++;
                            }
                            continue;
                        } else {
                            if (!\is_numeric($parameter) && (Str::contains(Str::toLower($parameter), 'join') || $this->_containsOperator($parameter))) continue;
                            $this->_query->bindValue($parameterId, $parameter);
                            $parameterId++;
                        }
                    }
                }

                if ($this->_query->execute()) {
                    if ($isSelectingRecords) {
                        $tables = [];
                        $models = [];
                        $fields = [];
                        $tempResults = [];
                        $tempResultModels = [];
                        $numberOfFields = $this->_query->columnCount();

                        if ($numberOfFields > 0) {
                            for($i = 0; $i < $numberOfFields; ++$i) {
                                $column = $this->_query->getColumnMeta($i);
                                $tableName = (!$objectOutputName ? $column['table'] : $objectOutputName);
                                $modelName = $inflection->singularize($tableName);

                                array_push($models, $modelName);
                                array_push($tables, $tableName);
                                array_push($fields, $column['name']);
                            }

                            while ($row = $this->_query->fetch(PDO::FETCH_NUM)) {
                                for($i = 0; $i < $numberOfFields; ++$i) {
                                    $rowValue = $row[$i];

                                    $columnInfo = $this->_query->getColumnMeta($i);
                                    if ($this->isNumber($columnInfo)) {
                                        $rowValue = \doubleval($rowValue);
                                    } else if ($this->isBool($columnInfo)) {
                                        $rowValue = \boolval($rowValue);
                                    }

                                    $tempResults[$tables[$i]][$fields[$i]] = $rowValue;
                                    $tempResultModels[$models[$i]][$fields[$i]] = $rowValue;
                                }

                                array_push($this->_data, $tempResultModels);
                            }
                        }
                    }
                } else {
                    $errorInfo = print_r($this->_query->errorInfo()['2'], true);
                    $this->_errorMessage = (IS_DEVELOPMENT ? 'Error executing query: ' . PHP_EOL . $procedureName . PHP_EOL . 'Reason: ' . $errorInfo : USER_FRIENDLY_ERROR_MESSAGE);
                    if (!IS_DEVELOPMENT) {
                        $this->_logger->log('Error executing ' . PHP_EOL . $procedureName . PHO_EOL . 'Reason: ' . $errorInfo);
                    }
                }
            }

            return $this->_data;
            
        } catch (PDOException $e) {
            $this->_errorMessage = (!IS_DEVELOPMENT ? USER_FRIENDLY_ERROR_MESSAGE : $e->getMessage());
            $this->_logger->log($e->getMessage());
            return null;
        }
    }

    /**
     * Runs a SELECT query against the given table and returns the result as array.
     * You can add relationships to the output of this query in the form ['relationships' => ['child' => [['primaryTable' => 'users', 'primaryField' => 'id', 'foreignTable' => 'user_sessions', 'foreignField' => 'user_id']]], ['children' => [['primaryTable' => 'users', 'primaryField' => 'id', 'foreignTable' => 'user_logs', 'foreignField' => 'user_id']]]]]
     * 
     * @param string $sql The SQL statement to execute. This can also be a stored procedure, DML or DDL statement.
     * @param array $parameters Values of the parameters used in your $sql variable.
     * 
     * @return array
     */
    public function fetch(string $table, array $parameters = []) : array {
        $this->rowCount = 0;
        $this->_errorMessage = '';
        
        if (!$this->pdo) {
            $this->_errorMessage = self::CANNOT_CONNECT;
            if (!IS_DEVELOPMENT) {
                $this->_logger->log($this->_errorMessage);
            }
            return [];
        }

        global $inflection;

        $this->_errorMessage = '';
        $this->_data = [];

        //***************** construct parts of the SQL statement ****************
        $joinClause     = $this->_getJoinClause($parameters);
        $whereClause    = $this->_getWhereClause($parameters);
        $bindable       = $this->_getBindable($parameters);
        $orderClause    = $this->_getOrderClause($parameters);
        $limitClause    = $this->_getLimitClause($parameters);
        $relationships  = $this->_getRelationships($parameters);

        // prepare sql statement
        $sql = "SELECT * FROM `{$table}`{$joinClause}{$whereClause}{$orderClause}{$limitClause}";
        if (!$sql) {
            $this->_errorMessage = 'SQL statement is required.';
            if (!IS_DEVELOPMENT) {
                $this->_logger->log($this->_errorMessage);
            }
            return [];
        }
        $sql = $this->_getCleanSQL($sql);

        $this->_errorMessage = '';
        try {
            if ($this->_query = $this->pdo->prepare($sql)) {
                $parameterId = 1;

                if ($bindable && count($bindable) > 0) {
                    foreach($bindable as $parameter) {
                        if (is_array($parameter)) continue;
                        if (Str::contains(Str::toLower($parameter), 'join')) continue;
                        $this->_query->bindValue($parameterId, $parameter);
                        $parameterId++;
                    }
                }

                if ($this->_query->execute()) {
                    $this->rowCount     = $this->_query->rowCount();
                    $this->_lastInsertId= $this->pdo->lastInsertId();
                                        
                    $tables = [];
                    $models = [];
                    $fields = [];
                    $tempResults = [];
                    $tempResultModels = [];
                    $numberOfFields = $this->_query->columnCount();

                    if ($numberOfFields > 0) {
                        for($i = 0; $i < $numberOfFields; ++$i) {
                            $column = $this->_query->getColumnMeta($i);
                            $tableName = $column['table'];
                            $modelName = $inflection->singularize($tableName);

                            array_push($models, $modelName);
                            array_push($tables, $tableName);
                            array_push($fields, $column['name']);
                        }

                        while ($row = $this->_query->fetch(PDO::FETCH_NUM)) {
                            for($i = 0; $i < $numberOfFields; ++$i) {
                                $rowValue = $row[$i];

                                $columnInfo = $this->_query->getColumnMeta($i);
                                if ($this->isNumber($columnInfo)) {
                                    $rowValue = \doubleval($rowValue);
                                } else if ($this->isBool($columnInfo)) {
                                    $rowValue = \boolval($rowValue);
                                }

                                $tempResults[$tables[$i]][$fields[$i]] = $rowValue;
                                $tempResultModels[$models[$i]][$fields[$i]] = $rowValue;
                            }

                            if ($relationships) {
                                
                                // one to one relationship
                                if (array_key_exists(self::RELATIONSHIP_CHILD, $relationships)) {
                                    foreach($relationships[self::RELATIONSHIP_CHILD] as $childRelationship) {
                                        $primaryTable = $childRelationship[self::RELATIONSHIP_PRIMARY_TABLE];
                                        $primaryField = $childRelationship[self::RELATIONSHIP_PRIMARY_FIELD];
                                        $foreignTable = $childRelationship[self::RELATIONSHIP_FOREIGN_TABLE];
                                        $foreignField = $childRelationship[self::RELATIONSHIP_FOREIGN_FIELD];

                                        $primaryTableSingular = $inflection->singularize($primaryTable);
                                        $foreignTableSingular = $inflection->singularize($foreignTable);

                                        $childRecord = $this->_getRelationshipData($childRelationship, $tempResults, $relationships);
                                        $tempResultModels[$primaryTableSingular][$foreignTableSingular] = ($childRecord ? $childRecord[0][$foreignTable] : null);
                                    }
                                }

                                // one to many relationship
                                if (array_key_exists(self::RELATIONSHIP_CHILDREN, $relationships)) {
                                    foreach($relationships[self::RELATIONSHIP_CHILDREN] as $childRelationship) {
                                        $primaryTable = $childRelationship[self::RELATIONSHIP_PRIMARY_TABLE];
                                        $primaryField = $childRelationship[self::RELATIONSHIP_PRIMARY_FIELD];
                                        $foreignTable = $childRelationship[self::RELATIONSHIP_FOREIGN_TABLE];
                                        $foreignField = $childRelationship[self::RELATIONSHIP_FOREIGN_FIELD];

                                        $primaryTableSingular = $inflection->singularize($primaryTable);
                                        $foreignTableSingular = $inflection->singularize($foreignTable);

                                        $childrenRecord = $this->_getRelationshipData($childRelationship, $tempResults, $relationships);
                                        if (!$childrenRecord) 
                                            $tempResultModels[$primaryTableSingular][$foreignTable] = null;
                                        else {
                                            foreach($childrenRecord as $childRecord) {
                                                $childRecordData = $childRecord[$foreignTable];

                                                // grand child one to one relationship
                                                if (array_key_exists(self::RELATIONSHIP_GRAND_CHILD, $relationships)) {
                                                    $this->_getGrandChildRecord($childRecordData, $relationships, $foreignTable);
                                                }
                                                
                                                // grand children one to many relationship
                                                if (array_key_exists(self::RELATIONSHIP_GRAND_CHILDREN, $relationships)) {
                                                    $this->_getGrandChildrenRecord($childRecordData, $relationships, $foreignTable);
                                                }

                                                $tempResultModels[$primaryTableSingular][$foreignTable][] = $childRecordData;
                                            }                                                
                                        }
                                    }
                                }
                            }

                            array_push($this->_data, $tempResultModels);
                        }
                    }
                } else {
                    $errorInfo = print_r($this->_query->errorInfo()['2'], true);
                    $this->_errorMessage = (IS_DEVELOPMENT ? 'Error executing query: ' . PHP_EOL . $sql . PHP_EOL . 'Reason: ' . $errorInfo : USER_FRIENDLY_ERROR_MESSAGE);
                    if (!IS_DEVELOPMENT) {
                        $this->_logger->log('Error executing ' . PHP_EOL . $sql . PHO_EOL . 'Reason: ' . $errorInfo);
                    }
                }
            }
        } catch (PDOException $e) {
            $this->_errorMessage = (!IS_DEVELOPMENT ? USER_FRIENDLY_ERROR_MESSAGE : $e->getMessage());
            $this->_logger->log($e->getMessage());
        }

        return $this->_data;
    }

    private function _getGrandChildRecord(array &$referenceRecord, array $relationships, string $foreignTable) {
        global $inflection;
        
        foreach($relationships[self::RELATIONSHIP_GRAND_CHILD] as $grandChildRelationship) {
            $grandChildPrimaryTableArray = \explode('.', $grandChildRelationship[self::RELATIONSHIP_PRIMARY_TABLE]);
            if ($grandChildPrimaryTableArray[0] == $foreignTable) {
                $grandChildPrimaryField = $grandChildRelationship[self::RELATIONSHIP_PRIMARY_FIELD];
                $grandChildForeignTable = $grandChildRelationship[self::RELATIONSHIP_FOREIGN_TABLE];
                $grandChildForeignField = $grandChildRelationship[self::RELATIONSHIP_FOREIGN_FIELD];

                $grandChildRecord = $this->_getInnerRelationshipData($referenceRecord[$grandChildPrimaryField], $grandChildForeignTable, $grandChildForeignField);
                if ($grandChildRecord) {
                    $foreignTableSingle = $inflection->singularize($grandChildForeignTable);
                    $referenceRecord[$foreignTableSingle] = $grandChildRecord[0][$foreignTableSingle];
                } else {
                    $referenceRecord[$foreignTableSingle] = null;
                }
            }
        }
    }

    private function _getGrandChildrenRecord(array &$referenceRecord, array $relationships, string $foreignTable) {
        global $inflection;
        
        foreach($relationships[self::RELATIONSHIP_GRAND_CHILDREN] as $grandChildRelationship) {
            $grandChildPrimaryTableArray = \explode('.', $grandChildRelationship[self::RELATIONSHIP_PRIMARY_TABLE]);
            if ($grandChildPrimaryTableArray[0] == $foreignTable) {
                $grandChildPrimaryField = $grandChildRelationship[self::RELATIONSHIP_PRIMARY_FIELD];
                $grandChildForeignTable = $grandChildRelationship[self::RELATIONSHIP_FOREIGN_TABLE];
                $grandChildForeignField = $grandChildRelationship[self::RELATIONSHIP_FOREIGN_FIELD];

                $grandChildRecord = $this->_getInnerRelationshipData($referenceRecord[$grandChildPrimaryField], $grandChildForeignTable, $grandChildForeignField);
                if ($grandChildRecord) {
                    $foreignTableSingle = $inflection->singularize($grandChildForeignTable);
                    $referenceRecord[$grandChildForeignTable][] = $grandChildRecord[0][$foreignTableSingle];
                } else {
                    $referenceRecord[$grandChildForeignTable] = null;
                }
            }
        }
    }

    private function _getInnerRelationshipData(string $referenceValue, string $foreignTable, string $foreignField) : array {
        global $inflection;
        $result = [];

        $sql = $this->_getCleanSQL("SELECT * FROM `{$foreignTable}` WHERE `$foreignField` = ?");
        if ($queryResult = $this->pdo->prepare($sql)) {
            $queryResult->bindValue(1, $referenceValue);

            if ($queryResult->execute()) {                
                $tables = [];
                $models = [];
                $fields = [];
                $temResult = [];
                $numberOfFields = $queryResult->columnCount();

                if ($numberOfFields > 0) {
                    for($i = 0; $i < $numberOfFields; $i++) {
                        $column = $queryResult->getColumnMeta($i);
                        $tableName = $column['table'];
                        $modelName = $inflection->singularize($tableName);

                        array_push($models, $modelName);
                        array_push($tables, $tableName);
                        array_push($fields, $column['name']);
                    }

                    while ($row = $queryResult->fetch(PDO::FETCH_NUM)) {
                        for($i = 0; $i < $numberOfFields; ++$i) {
                            $rowValue = $row[$i];

                            $columnInfo = $queryResult->getColumnMeta($i);
                            if ($this->isNumber($columnInfo)) {
                                $rowValue = \doubleval($rowValue);
                            } else if ($this->isBool($columnInfo)) {
                                $rowValue = \boolval($rowValue);
                            }

                            $temResult[$models[$i]][$fields[$i]] = $rowValue;
                        }

                        array_push($result, $temResult);
                    }
                }
            } else {
                $errorInfo = print_r($queryResult->errorInfo()['2'], true);
                $this->_errorMessage = (IS_DEVELOPMENT ? 'Error executing query: ' . PHP_EOL . $sql . PHP_EOL . 'Reason: ' . $errorInfo : USER_FRIENDLY_ERROR_MESSAGE);
                if (!IS_DEVELOPMENT) {
                    $this->_logger->log('Error executing ' . PHP_EOL . $sql . PHO_EOL . 'Reason: ' . $errorInfo);
                }
            }
        }

        return $result;
    } 

    private function _getRelationshipData(array $childRelationship, array $tempResults, array $relationships) : array {
        global $inflection;
        $result = [];

        $primaryTable   = $childRelationship[self::RELATIONSHIP_PRIMARY_TABLE];
        $primaryField   = $childRelationship[self::RELATIONSHIP_PRIMARY_FIELD];
        $foreignTable   = $childRelationship[self::RELATIONSHIP_FOREIGN_TABLE];
        $foreignField   = $childRelationship[self::RELATIONSHIP_FOREIGN_FIELD];
        $orderBy        = $childRelationship['order'];

        $tableNames = array_keys($tempResults);
        foreach($tableNames as $_tableName) {
            if ($_tableName == $primaryTable) {
                $childSql = "SELECT * FROM `{$foreignTable}` WHERE `{$foreignField}` = ?" . ($orderBy ? " ORDER BY {$orderBy}" : "");
                $childSql = $this->_getCleanSQL($childSql);

                $primaryFieldValue = $tempResults[$primaryTable][$primaryField];

                if ($childQuery = $this->pdo->prepare($childSql)) {
                    $childQuery->bindValue(1, $primaryFieldValue);

                    if ($childQuery->execute()) {
                        $childTables = [];
                        $childModels = [];
                        $childFields = [];
                        $childTempResults = [];
                        $childNumberOfFields = $childQuery->columnCount();

                        if ($childNumberOfFields > 0) {
                            for($i = 0; $i < $childNumberOfFields; ++$i) {
                                $childColumn = $childQuery->getColumnMeta($i);
                                $childTableName = $childColumn['table'];
                                $childModelName = $inflection->singularize($childTableName);

                                array_push($childModels, $childModelName);
                                array_push($childTables, $childTableName);
                                array_push($childFields, $childColumn['name']);
                            }

                            while ($row = $childQuery->fetch(PDO::FETCH_NUM)) {
                                for($i = 0; $i < $childNumberOfFields; ++$i) {
                                    $rowValue = $row[$i];

                                    $columnInfo = $childQuery->getColumnMeta($i);
                                    if ($this->isNumber($columnInfo)) {
                                        $rowValue = \doubleval($rowValue);
                                    } else if ($this->isBool($columnInfo)) {
                                        $rowValue = \boolval($rowValue);
                                    }
                                    
                                    $childTempResults[$childTables[$i]][$childFields[$i]] = $rowValue;
                                }

                                array_push($result, $childTempResults);
                            }
                        }
                    } else {
                        $errorInfo = print_r($this->_query->errorInfo()['2'], true);
                        $this->_errorMessage = (IS_DEVELOPMENT ? 'Error executing query: ' . PHP_EOL . $childSql . PHP_EOL . 'Reason: ' . $errorInfo : USER_FRIENDLY_ERROR_MESSAGE);
                        if (!IS_DEVELOPMENT) {
                            $this->_logger->log('Error executing ' . PHP_EOL . $childSql . PHO_EOL . 'Reason: ' . $errorInfo);
                        }
                    }
                }
            }
        }

        return $result;
    }

    function isNumber(array $columnInfo) : bool {
        return $columnInfo['native_type'] === 'LONG';
    }

    function isBool(array $columnInfo) : bool {
        return \in_array($columnInfo['native_type'], ['TINY', 'BIT']);
    }

    private function _isValidForInsertOrUpdate(string $table, array $fields = []) : bool {
        if (!$table) {
            $this->_errorMessage = 'Table name is required.';
            return false;
        }
        if (!$fields || ($fields && count($fields) == 0)) {
            $this->_errorMessage = 'Field and corresponding values must be specified.';
            return false;
        }
        if (!Ary::isAssociative($fields)) {
            $this->_errorMessage = 'Fields parameter is expected to be an associative array in the form [\'field\' => \'value\'].';
            return false;
        }

        return true;
    }

    /**
     * Insert new record into the table specified with the given fields.
     * 
     * @param string $table Specifies the table name to insert data into.
     * @param array $fields The parameters in form of associative array, format: [\'field\' => \'value\'].
     * 
     * @return void
     */
    public function insert(string $table, array $fields = []) {
        try {
            // some validations
            if (!$this->_isValidForInsertOrUpdate($table, $fields)) return;

            // prepare the fields and values for insert
            $fieldsString   = '';
            $valuesString   = '';
            $values         = [];
            foreach($fields as $field => $value) {
                $fieldsString .= '`' . $field . '`,';
                $valuesString .= '?,';
                array_push($values, $value);
            }
            $fieldsString = rtrim($fieldsString, ',');
            $valuesString = rtrim($valuesString, ',');

            $sql = "INSERT INTO `{$table}` " . PHP_EOL . "({$fieldsString}) " . PHP_EOL . "VALUES ({$valuesString})";

            // execute the query
            $this->query($sql, $values);
        } catch (PDOException $e) {
            $this->_errorMessage = (!IS_DEVELOPMENT ? USER_FRIENDLY_ERROR_MESSAGE : $e->getMessage());
            $this->_logger->log($e->getMessage());
        }
    }

    /**
     * Updates existing record in the table specified with the given fields.
     * 
     * @param string $table Specifies the table name to update data into.
     * @param mixed $idValue Specifies the value of the ID field or primary field to be used for update.
     * @param array $fields The parameters in form of associative array, format: [\'field\' => \'value\'].
     * 
     * @return void
     */
    public function update(string $table, $idValue, array $fields = []) {
        try {
            // some validations
            if (!$this->_isValidForInsertOrUpdate($table, $fields)) return;
            if (!$idValue) {
                $this->_errorMessage = 'Primary key value to be used for the update operation is required.';
                return;
            }

            // prepare the fields and values for update
            $fieldsString   = '';
            $values         = [];
            foreach($fields as $field => $value) {
                $fieldsString .= '`' . $field . '` = ?,';
                array_push($values, $value);
            }
            $fieldsString = rtrim($fieldsString, ',');

            $sql = "UPDATE `{$table}` " . PHP_EOL . "SET {$fieldsString} " . PHP_EOL . "WHERE `{$this->_idField}` = ?";
            array_push($values, $idValue);

            // execute the query
            $this->query($sql, $values);
        } catch (PDOException $e) {
            $this->_errorMessage = (!IS_DEVELOPMENT ? USER_FRIENDLY_ERROR_MESSAGE : $e->getMessage());
            $this->_logger->log($e->getMessage());
        }
    }

    public function delete(string $table, $idValue) {
        try {
            // some validations
            if (!$table) {
                $this->_errorMessage = 'Table name is required.';
                return;
            }
            if (!$idValue) {
                $this->_errorMessage = 'Primary key value to be used for the delete operation is required.';
                return;
            }

            // prepare for delete
            $values = [];
            $sql = "DELETE FROM `{$table}` " . PHP_EOL . "WHERE `{$this->_idField}` = ?";
            array_push($values, $idValue);
            //echo $sql; dnd($bindable);
            // execute the query
            $this->query($sql, $values);
        } catch (PDOException $e) {
            $this->_errorMessage = (!IS_DEVELOPMENT ? USER_FRIENDLY_ERROR_MESSAGE : $e->getMessage());
            $this->_logger->log($e->getMessage());
        }
    }

    public function select(string $table, array $parameters = []) {
        return $this->_read($table, $parameters);
    }

    public function find(string $table, array $parameters = []) {
        $rows = $this->_read($table, $parameters);
        if (!$rows) return null;
        return $rows[0];
    }

    public function count(string $table, string $field, array $parameters = []) {
        //***************** construct parts of the SQL statement ****************
        $joinClause     = $this->_getJoinClause($parameters);
        $whereClause    = $this->_getWhereClause($parameters);
        $bindable       = $this->_getBindable($parameters);
        $orderClause    = $this->_getOrderClause($parameters);
        $limitClause    = $this->_getLimitClause($parameters);

        // prepare sql statement
        $sql = PHP_EOL . "SELECT COUNT({$field}) AS `record_count` FROM `{$table}`{$joinClause}{$whereClause}{$orderClause}{$limitClause}";
        //echo $sql; dnd($bindable);
        // execute and return execution result
        return intval($this->_execute($sql, $bindable)[0]->record_count);
    }

    private function _read(string $table, array $parameters = []) {        
        //***************** construct parts of the SQL statement ****************
        $joinClause         = $this->_getJoinClause($parameters);
        $whereClause        = $this->_getWhereClause($parameters);
        $bindable           = $this->_getBindable($parameters);
        $orderClause        = $this->_getOrderClause($parameters);
        $limitClause    = $this->_getLimitClause($parameters);

        // prepare sql statement
        $sql = PHP_EOL . "SELECT * FROM `{$table}`{$joinClause}{$whereClause}{$orderClause}{$limitClause}";
        //echo $sql; dnd($bindable);
        // execute and return execution result
        return $this->_execute($sql, $bindable);
    }

    private function _getRelationships(array $parameters) : array {
        $relationships = [];

        $arrayKeyRelationships = 'relationships';
        if ($parameters && array_key_exists($arrayKeyRelationships, $parameters)) {
            $relationships = $parameters[$arrayKeyRelationships];
        }

        return $relationships;
    }

    private function _getJoinClause(array $parameters = []) : string {
        $joinClause = '';
        $arrayKeyJoins      = 'joins';

        if ($parameters) {
            // check for joins
            if (array_key_exists($arrayKeyJoins, $parameters)) {
                if (is_array($parameters[$arrayKeyJoins])) {
                    foreach($parameters[$arrayKeyJoins] as $join) {
                        $joinClause .= ' ' . $join;
                    }
                    $joinClause = PHP_EOL . trim($joinClause);
                } else {
                    $joinClause = PHP_EOL . $parameters[$arrayKeyJoins];
                }
            }
        }

        return $joinClause;
    }

    private function _getWhereClause(array $parameters = []) : string {
        $whereClause= '';
        $arrayKeyConditions = 'conditions';
        
        if ($parameters) {
			// check for conditions
            if (array_key_exists($arrayKeyConditions, $parameters)) {
                if (is_array($parameters[$arrayKeyConditions])) {
                    foreach($parameters[$arrayKeyConditions] as $condition) {
                        if (\in_array(trim($condition), [Database::AND, Database::OR])) {
                            if (!Str::contains($whereClause, $condition))
                                $whereClause .= $condition;
                        } else {
                            if (!Str::contains($whereClause, $condition))
                                $whereClause .= ' ' . $condition . PHP_EOL;
                        }
                    }
                    $whereClause = trim($whereClause);
                } else {
                    if (!Str::contains($whereClause, $condition))
                        $whereClause = $parameters[$arrayKeyConditions];
                }

                if ($whereClause) {
                    $whereClause = PHP_EOL . 'WHERE ' . $whereClause;
                }
            }
        }

        return $whereClause;
    }

    private function _getBindable(array $parameters = []) : array {
        $bindable           = [];
        $arrayKeyBindable   = 'bindable';
        
        if ($parameters) {
            // check for bindable
            if (array_key_exists($arrayKeyBindable, $parameters)) {
                $bindable = $parameters[$arrayKeyBindable];
            }
        }

        return $bindable;
    }

    private function _getOrderClause(array $parameters = []) : string {
        $orderClause    = '';
        $arrayKeyOrder  = 'order';
        
        if ($parameters) {
            // check for order clause
            if (array_key_exists($arrayKeyOrder, $parameters)) {
                $orderClause = PHP_EOL . ' ORDER BY ' . $parameters[$arrayKeyOrder];
            }
        }

        return $orderClause;
    }

    private function _getLimitClause(array $parameters = []) : string {
        $limitClause    = '';        
        $arrayKeyLimit  = 'limit';

        if ($parameters) {
            // check for limit clause
            if (array_key_exists($arrayKeyLimit, $parameters)) {
                $limitClause = PHP_EOL . ' LIMIT ' . $parameters[$arrayKeyLimit];
            }
        }

        return $limitClause;
    }

    private function _execute($sql, $bindable) {
        $this->query($sql, $bindable);
        if ($this->hasError()) return null;

        return $this->getResult();
    }

    public function startTransaction() {
        $this->rowCount = 0;
        $this->_errorMessage = '';
        
        if (!$this->pdo) {
            $this->_errorMessage = self::CANNOT_CONNECT;
            return;
        }

        $this->pdo->beginTransaction();
    }

    public function commitTransaction() {
        $this->rowCount = 0;
        $this->_errorMessage = '';
        
        if (!$this->pdo) {
            $this->_errorMessage = self::CANNOT_CONNECT;
            return;
        }

        $this->pdo->commit();
    }
    
    public function rollbackTransaction() {
        $this->rowCount = 0;
        $this->_errorMessage = '';
        
        if (!$this->pdo) {
            $this->_errorMessage = self::CANNOT_CONNECT;
            return;
        }

        $this->pdo->rollback();
    }

}