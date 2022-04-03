<?php

require_once "config/config.php";

class PDODbHelper {

    /**
     * @var PDO
     */
    private static $link;

    public function __construct() {
        $dsn = sprintf("mysql:dbname=%s;host=%s", DB_NAME, DB_HOST);

        try {
            self::$link = new PDO($dsn, DB_USER, DB_PASS);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    /**
     * @param string $table
     * @param array $parameters
     * @return bool
     */
    public function insert($table, $parameters) {

        $columns = "";
        $values = "";
        $v = [];
        foreach ($parameters as $key => $value) {
            $columns .= "$key, ";
            $values .= ":$key, ";
            $v[":$key"] = $value;
        }

        $columns = rtrim($columns, ", ");
        $values = rtrim($values, ", ");

        $sql = "INSERT INTO $table ($columns) VALUES ($values)";
        $stmt = self::$link->prepare($sql);

        return $stmt->execute($v);
    }

    /**
     * @param string $table
     * @param array $columns
     * @param array $whereParams
     * @return array|false
     * @noinspection SqlConstantExpression
     * @noinspection SqlConstantCondition
     */
    public function select($table, $columns, $whereParams = []) {

        $col_str = count($columns) > 0 ? implode(", ", $columns) : "*";

        $where_clause = "";
        foreach ($whereParams as $key => $value) {
            $where_clause .= " AND $key = '$value'";
        }

        $sql = "SELECT $col_str FROM $table WHERE 1=1 $where_clause";
        $stmt = self::$link->prepare($sql);

        $result = $stmt->execute();

        if (!$result) return false;

        $rows = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = $row;
        }

        return $rows;

    }

    /**
     * @param string $table
     * @param array $params
     * @param array $wherePrams
     * @return boolean
     * @noinspection SqlConstantExpression
     * @noinspection SqlConstantCondition
     */
    public function update($table, $params, $wherePrams) {
        $fieldsStr = "";
        $whereStr = "";

        foreach ($params as $key => $value) {
            $fieldsStr .= "$key=:$key, ";
        }

        foreach ($wherePrams as $key => $value) {
            if (isset($params[$key])) {
                $whereStr .= " AND $key=:{$key}_1";
                continue;
            }
            $whereStr .= " AND $key=:$key";
        }

        $fieldsStr = rtrim($fieldsStr, ", ");

        $combined = self::mergeArrays($params, $wherePrams);

        $sql = "UPDATE $table SET $fieldsStr WHERE 1=1$whereStr";

        $stmt = self::$link->prepare($sql);
        return $stmt->execute($combined);
    }

    /**
     *
     * This function deletes a record from the database
     *
     * in order to work with this function you should provide a table's name and an associative array
     *
     * The first associative array $whereParams should contain the database column's names
     * and what should they equal, for example:
     *
     * if you want this expression " ... WHERE id = 2 AND title = "example"
     * the array then should be like the following :
     *
     * $whereParams = ["id" => 2, "title" => "example"]
     *
     *
     * @param string $table
     * @param array $whereParams
     * @return boolean
     * @noinspection SqlConstantExpression
     * @noinspection SqlConstantCondition
     */
    public function delete($table, $whereParams) {

        $whereStr = "";

        foreach ($whereParams as $key => $value) {
            $whereStr .= " AND $key=:$key";
        }

        $sql = "DELETE FROM $table WHERE 1=1$whereStr";
        $stmt = self::$link->prepare($sql);
        return $stmt->execute($whereStr);
    }

    /**
     *
     * Merges two associative arrays, keeping the duplicate keys (Adding `_1` at its end)
     *
     * @param $array1
     * @param $array2
     * @return array
     */
    private static function mergeArrays($array1, $array2) {
        foreach ($array2 as $key => $value) {
            if (isset($array1[$key])) {
                $array1[$key . "_1"] = $value;
                continue;
            }
            $array1[$key] = $value;
        }
        return $array1;
    }

    public function __destruct() {
        self::$link = null;
    }

}
