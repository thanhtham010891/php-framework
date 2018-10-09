<?php

namespace System\Contract\Database;

interface ConnectionInterface
{
    /**
     * @param string $name
     * @return void
     */
    public function openConnect($name = 'default');

    /**
     * @param string $name
     * @return void
     */
    public function closeConnect($name = 'default');

    /**
     * @param string $name
     * @return void
     */
    public function reConnect($name = 'default');

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