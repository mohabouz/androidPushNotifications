<?php

require_once "config/config.php";

class Database {

    /**
     * @var mysqli
     */
    public static $link;

    public function __construct() {
        self::$link = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if (self::$link->connect_error) {
            die("Connection failed: " . self::$link->connect_error);
        }
    }

    /**
     * @param string $table
     * @param array $where
     * @return array|boolean
     * @noinspection SqlConstantCondition
     * @noinspection SqlConstantExpression
     */
    public function select($table, $where = []) {

        $w = self::_get_where_str($where);

        $sql = "SELECT * FROM {$table} WHERE 1=1 {$w}" ;

        $result = self::$link->query($sql);

        if ($result) {
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        }

        return false;
    }

    /**
     * @param string $table
     * @param array $parameters
     * @return boolean
     */
    public function insert($table, $parameters) {

        $columns = "";
        $values = "";

        foreach ($parameters as $key => $value) {
            $key = self::$link->real_escape_string($key);
            $value = self::$link->real_escape_string($value);
            $columns .= "{$key}, ";
            $values .= "'{$value}', ";
        }

        $columns = rtrim($columns, ", ");
        $values = rtrim($values, ", ");

        $sql = "INSERT INTO {$table} ($columns) VALUES ($values)";

        $result = self::$link->query($sql);

        return $result;

    }

    /**
     * @param string $table
     * @param array $parameters
     * @param array $where
     * @return boolean
     * @noinspection SqlConstantCondition
     * @noinspection SqlConstantExpression
     */
    public function update($table, $parameters, $where) {
        $w = self::_get_where_str($where);
        $p = "";

        foreach ($parameters as $key => $value) {
            $key = self::$link->real_escape_string($key);
            $value = self::$link->real_escape_string($value);
            $p .= "{$key}={$value}, ";
        }

        $p = rtrim($p, ", ");

        $sql = "UPDATE {$table} SET {$p} WHERE 1=1 {$w}";
        $result = self::$link->query($sql);
        self::$link->close();

        return $result;
    }

    /**
     * @param array $where
     * @return string
     */
    private static function _get_where_str(array $where) {
        $w = "";
        foreach ($where as $key => $value) {
            $key = self::$link->real_escape_string($key);
            $value = self::$link->real_escape_string($value);
            $w .= " and {$key} = '{$value}'";
        }
        return $w;
    }


}
