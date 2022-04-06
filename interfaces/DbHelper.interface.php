<?php

interface DbHelper {
    /**
     * @param string $table
     * @param array $parameters
     * @return bool
     */
    public function insert($table, $parameters);

    /**
     * @param string $table
     * @param array $columns
     * @param array $whereParams
     * @return array|false
     */
    public function select($table, $columns, $whereParams = []);

    /**
     * @param string $table
     * @param array $columns
     * @param array $wherePrams
     * @return boolean
     */
    public function update($table, $columns, $wherePrams);

    /**
     * @param string $table
     * @param array $whereParams
     * @return boolean
     */
    public function delete($table, $whereParams);

    /**
     * @return false|string
     */
    public function getLastInsertedId();
}