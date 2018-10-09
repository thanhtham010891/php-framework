<?php

namespace App\Providers\Database;

use System\Contract\Database\ConnectionInterface;
use System\Contract\Database\DatabaseInterface;
use App\Providers\Database\Connection\ConnectionException;

class Database implements DatabaseInterface
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @var string
     */
    private $currentDatabase = 'master';

    /**
     * @param string $name
     * @param ConnectionInterface $connect
     */
    public function setConnection($name = 'master', ConnectionInterface $connect)
    {
        $this->connection[$name] = $connect;
    }

    /**
     * @param string $name
     * @return ConnectionInterface
     * @throws ConnectionException
     */
    public function getConnection($name = 'master')
    {
        if (empty($this->connection[$name])) {
            throw new ConnectionException('Connection `' . $name . '` is empty');
        }

        /**
         * @var ConnectionInterface $connection
         */
        $connection = $this->connection[$name];

        $connection->openConnect();

        $this->currentDatabase = $name;

        return $connection;
    }

    /**
     * @return string
     * @throws ConnectionException
     */
    public function getDatabaseName()
    {
        return $this->getConnection()->getDatabaseName();
    }

    public function __construct(ConnectionInterface $connection = null)
    {
        if (!empty($connection)) {
            $this->setConnection('master', $connection);
        }
    }

    /**
     * @param string $sql
     * @param array $params
     * @param string $fetchClass
     * @return mixed|\Object
     * @throws ConnectionException
     */
    public function fetchOne($sql, array $params = [], $fetchClass = "")
    {
        return $this->getConnection($this->currentDatabase)->fetchOne($sql, $params, $fetchClass, []);
    }

    /**
     * @param string $sql
     * @param array $params
     * @param string $fetchClass
     * @return array
     * @throws ConnectionException
     */
    public function fetchAll($sql, array $params = [], $fetchClass = "")
    {
        return $this->getConnection($this->currentDatabase)->fetchAll($sql, $params, $fetchClass, []);
    }

    /**
     * @param string $sql
     * @param array $params
     * @return bool
     * @throws ConnectionException
     */
    public function execute($sql, array $params = [])
    {
        return $this->getConnection($this->currentDatabase)->execute($sql, $params);
    }
}