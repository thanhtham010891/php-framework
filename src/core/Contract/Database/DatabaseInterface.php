<?php

namespace System\Contract\Database;

interface DatabaseInterface
{
    /**
     * @param string $name
     * @param ConnectionInterface $connect
     */
    public function setConnection($name = 'master', ConnectionInterface $connect);

    /**
     * @param string $name
     * @return ConnectionInterface
     */
    public function getConnection($name = 'master');

    /**
     * @return string
     */
    public function getDatabaseName();

    /**
     * @param string $sql
     * @param array $params
     * @param string $fetchClass
     * @return \Object
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