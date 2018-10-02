<?php

namespace System\Contract\Database;

use System\Contract\ServiceInterface;

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
     * @return string
     */
    public function getDatabaseName();

    /**
     * @param string $sql
     * @param array $params
     * @param string $fetchClass
     * @return Object
     */
    public function fetchOne($sql, array $params = [], $fetchClass = "");

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