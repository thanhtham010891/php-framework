<?php

namespace App\Core\Contract\Database;

use App\Core\Contract\ServiceInterface;

interface DatabaseInterface extends ServiceInterface
{
    /**
     * @param ConnectionInterface $connect
     */
    public function setConnection(ConnectionInterface $connect);

    /**
     * @return ConnectionInterface
     */
    public function getConnection();

    /**
     * @param string $sql
     * @param array $params
     * @param string $fetchClass
     * @return Object
     */
    public function fetch($sql, array $params = [], $fetchClass = "");

    /**
     * @param string $sql
     * @param array $params
     * @param string $fetchClass
     * @return array
     */
    public function fetchAll($sql, array $params = [], $fetchClass = "");

    /**
     * @param string $sql
     * @param array $params
     * @return bool
     */
    public function execute($sql, array $params = []);
}