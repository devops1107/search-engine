<?php

namespace spark\models;

use Slim\PDO\Statement\StatementContainer;
use Slim\Slim;

/**
* Base Model Class, all Models should extend this class
*
* @version 0.1
* @author MirazMac <mirazmac@gmail.com>
* @link https://mirazmac.info <Author Homepage>
*/
class Model
{
    const DB_ERROR = 'Unknown DB Error Occured. Please check the log files for more information.';

    /**
     * Database Prefix
     *
     * @var string
     */
    protected static $prefix;

    /**
     * The table name, without any prefix
     *
     * @var string
     */
    protected static $table;

    /**
     * Sorting rules
     *
     * @var array
     */
    protected $sortRules = [];

    /**
     * Name of the default query key to be used in the CRUD methods
     *
     * @var string
     */
    protected $queryKey = 'id';


    /**
     * Automatically append 'updated_at', 'created_at' field with
     * current timestamp to self::update(), self::insert() respectively
     *
     * @var boolean
     */
    protected $autoTimestamp = false;

    /**
     * Whether to return the active query or just execute it
     * Applies to the CRUD methods only
     *
     * @var boolean
     */
    protected $returnQuery = false;

    /**
     * Does nothing, is here incase someone calls parent::__construct();
     */
    public function __construct()
    {
    }

    public function __get($var)
    {
        if (isset($this->{$var})) {
            return $this->{$var};
        }
    }

    public function db()
    {
        return Slim::getInstance()->db;
    }

    /**
     * Return the query instead of executing it
     * Applies to the CRUD methods only
     *
     * @param  boolean $return
     * @return [type]
     */
    public function return($return = true)
    {
        $this->returnQuery = (bool) $return;
        return $this;
    }

    /**
     * Create a new row
     *
     * @param  array  $data Array of data to be inserted
     * @return boolean
     */
    public function create(array $data)
    {
        if ($this->autoTimestamp) {
            $time = time();

            if (empty($data['created_at'])) {
                $data['created_at'] = $time;
            }

            if (empty($data['updated_at'])) {
                $data['updated_at'] = $time;
            }
        }

        $db = Slim::getInstance()->db;
        $table = $this->getTable();
        $sql = $db->insert(array_keys($data))->into($table)->values(array_values($data));

        if ($this->returnQuery) {
            // Return back to old state
            $this->returnQuery = false;
            return $sql;
        }

        $id = $sql->execute();

        return $id;
    }

    /**
     * Get item by its query key
     *
     * @param  mixed $queryKeyValue     The item query key value
     * @param  array   $fields List of fields to retrieve
     * @return array
     */
    public function read($queryKeyValue, array $fields = ['*'], array $filters = [])
    {
        $sql = $this->select($fields)
               ->where($this->queryKey, '=', $queryKeyValue);

        $sql = $this->applyModelFilters($sql, $filters);

        $sql = $sql->limit(1, 0);

        if ($this->returnQuery) {
            // Return back to old state
            $this->returnQuery = false;
            return $sql;
        }

        $stmt = $sql->execute();
        return $stmt->fetch();
    }

    /**
     * Update a row by it's primary key
     *
     * @param  integer $queryKeyValue   The item primary key
     * @param  array  $data  The data to update
     * @return boolean
     */
    public function update($queryKeyValue, array $data)
    {
        if ($this->autoTimestamp) {
            // In case you need to prevent auto update timestamp
            if (isset($data['updated_at']) && !$data['updated_at']) {
                unset($data['updated_at']);
            } else {
                $data['updated_at'] = time();
            }
        }

        $db = Slim::getInstance()->db;
        $table = $this->getTable();
        $sql = $db->update($data)
              ->table($table)
              ->where($this->queryKey, '=', $queryKeyValue);

        if ($this->returnQuery) {
            // Return back to old state
            $this->returnQuery = false;
            return $sql;
        }

        $status = $sql->execute();
        return $status;
    }

    /**
     * Delete a row by it's primary key
     *
     * @param  integer $queryKeyValue   The item query key value
     * @return boolean
     */
    public function delete($queryKeyValue)
    {
        $db = Slim::getInstance()->db;
        $table = $this->getTable();
        $sql = $db->delete()
              ->from($table)
              ->where($this->queryKey, '=', $queryKeyValue);

        if ($this->returnQuery) {
            // Return back to old state
            $this->returnQuery = false;
            return $sql;
        }

        $status = $sql->execute();

        return $status;
    }

    /**
     * Helper for the select query with table and fields pre provided,
     * useful for quick select/count query outside the model
     *
     * @param  array  $fields Array of fields to select
     * @return SelectStatement
     */
    public function select(array $fields = ['*'])
    {
        return Slim::getInstance()->db->select($fields)->from($this->getTable());
    }

    /**
     * Read multiple items
     *
     * @param  array   $fields       List of fields to return
     * @param  integer $offset       Offset value
     * @param  integer $itemsPerPage Number of items per page
     * @param  array   $filters      Sort filters to apply to the query
     * @return array|boolean
     */
    public function readMany(array $fields = ['*'], $offset = 0, $itemsPerPage = 10, $filters = [])
    {
        $sql = $this->select($fields);
        $sql = $this->applyModelFilters($sql, $filters);
        $sql = $sql->limit($itemsPerPage, $offset);

        if ($this->returnQuery) {
            // Return back to old state
            $this->returnQuery = false;
            return $sql;
        }


        $stmt = $sql->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get count of all the rows
     *
     * @param  string $queryKey The key to use in count, defaults to null which leads to $this->queryKey
     * @param  array $filters Filters (optional)
     * @return integer
     */
    public function countRows($queryKey = null, array $filters = [])
    {
        if (is_null($queryKey)) {
            $queryKey = $this->queryKey;
        }

        $sql = $this->select(["COUNT( {$queryKey} ) as count"]);
        $sql = $this->applyModelFilters($sql, $filters);
        $row = $sql->execute()->fetch();
        return $row['count'];
    }

    public function applyModelFilters(StatementContainer $sql, array $filters)
    {
        if (isset($filters['sort'])) {
            $sql = $this->applySortFilter($sql, $filters['sort']);
        }

        if (isset($filters['where']) && is_array($filters['where'])) {
            $sql = $this->applyWhereFilter($sql, $filters['where']);
        }

        return $sql;
    }

    public function applySortFilter(StatementContainer $sql, $sort)
    {
        $sort = $this->getSortRule($sort);
        foreach ($sort as $row => $order) {
            // if we have a dot that means we have a specified table sorting already
            if (!strpos($row, '.')) {
                // else we'd add the current table name to avoid ambiguous errors in join queries
                $row = $this->getTable() . '.' . $row;
            }
            $sql = $sql->orderBy($row, $order);
        }

        return $sql;
    }

    public function applyWhereFilter(StatementContainer $sql, array $where)
    {
        foreach ($where as $condition) {
            if (is_array($condition) && count($condition) >= 3) {
                $key = $condition[0];
                $operator = $condition[1];
                $compareTo = $condition[2];
                $chainType = 'AND';

                if (isset($condition[3]) && strtolower($condition[3]) === 'or') {
                    $chainType = 'OR';
                }
            } else {
                continue;
            }

            $sql = $sql->where($key, $operator, $compareTo, $chainType);
        }

        return $sql;
    }

    /**
     * Check if a row exist by its query key value
     *
     * @return boolean
     */
    public function exists()
    {
        $args = func_get_args();
        $argsNum = func_num_args();

        if ($argsNum === 1) {
            $queryKey = $this->queryKey;
            $queryKeyValue = $args[0];
        } elseif ($argsNum === 2) {
            $queryKey = $args[0];
            $queryKeyValue = $args[1];
        } else {
            throw new \InvalidArgumentException(__METHOD__ . "allows min. 1 and max. 2 arguments, {$argsNum} provided!");
        }

        $stmt = $this->select(["COUNT( {$queryKey} ) as count"])
                ->where($queryKey, '=', $queryKeyValue)
                ->execute();
        $row = $stmt->fetch();
        return (bool) $row['count'];
    }


    /**
     * Get a row by matching field value
     *
     * @param  string $key
     * @param  mixed $equalsTo
     * @param  array  $fields
     * @return mixed
     */
    public function fetchRow($key, $equalsTo, array $fields = ['*'])
    {
        $sql = $this->select($fields)
               ->where($key, '=', $equalsTo)
               ->limit(1, 0);
        $stmt = $sql->execute();
        return $stmt->fetch();
    }

    /**
     * Delete a row by matching field value
     *
     * @param  string $key
     * @param  mixed  $equalsTo
     * @param  array  $filters
     * @return mixed
     */
    public function deleteRow($key, $equalsTo, array $filters = [])
    {
        $db = Slim::getInstance()->db;
        $table = $this->getTable();
        $sql = $db->delete()
              ->from($table)
              ->where($key, '=', $equalsTo);
        $sql = $this->applyModelFilters($sql, $filters);
        return $sql->execute();
    }

    /**
     * Truncate/empty current table
     *
     * @return boolean
     */
    public function truncate()
    {
        $db = Slim::getInstance()->db;
        $table = $this->getTable();
        return $db->query("TRUNCATE TABLE `{$table}`");
    }

    /**
     * Check if sort type is valid
     *
     * @param  string  $type The sort type
     * @return boolean
     */
    public function isSortAllowed($type)
    {
        return isset($this->sortRules[$type]) && is_array($this->sortRules[$type]);
    }

    /**
     * Get sort rule for a specific key
     *
     * @param  string $sort
     * @return array
     */
    public function getSortRule($sort)
    {
        if (!$this->isSortAllowed($sort)) {
            return [];
        }

        return $this->sortRules[$sort];
    }

    /**
     * Get allowed sort rule names as array
     *
     * @return array
     */
    public function getAllowedSorting()
    {
        return array_keys($this->sortRules);
    }

    /**
     * Get the table name with prefix
     *
     * @return string
     */
    public static function getTable()
    {
        return static::getPrefix(static::$table);
    }

    /**
     * Get the prefix with table name (optional)
     *
     * @param  string $table Optional table name to be appended
     * @return string
     */
    public static function getPrefix($table = null)
    {
        return static::$prefix . $table;
    }

    /**
     * Set the prefix
     *
     * @param string $prefix
     */
    public static function setPrefix($prefix)
    {
        static::$prefix = $prefix;
    }

    public function getModelName()
    {
        return basename(get_class($this));
    }
}
