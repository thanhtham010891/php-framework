<?php

namespace App\Core\Contract\Database;

use PDO;

interface ConnectionInterface
{
    /**
     * @return void
     */
    public function openConnect();

    /**
     * @return void
     */
    public function closeConnect();

    /**
     * @return PDO
     */
    public function getResource();

    /**
     * @return string
     */
    public function getDatabaseName();

    /**
     * @param string $sql
     * @param array $params
     * @param string $fetchClass
     * @param array $fetchClassArgs
     * @return mixed
     */
    public function fetchOne($sql, array $params = [], $fetchClass = "", $fetchClassArgs = []);

    /**
     * @param string $sql
     * @param array $params
     * @param string $fetchClass
     * @param array $fetchClassArgs
     * @return array
     */
    public function fetchAll($sql, array $params = [], $fetchClass = "", $fetchClassArgs = []);

    /**
     * @param string $sql
     * @param array $params
     * @return bool
     */
    public function execute($sql, array $params = []);
}