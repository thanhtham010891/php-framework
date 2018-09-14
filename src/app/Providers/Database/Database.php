<?php

namespace App\Providers\Database;

use App\Core\Contract\Database\ConnectionInterface;
use App\Core\Contract\Database\DatabaseInterface;

class Database implements DatabaseInterface
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    public function setConnection(ConnectionInterface $connect)
    {
        $this->connection = $connect;
    }

    public function getConnection()
    {
        if (empty($this->connection->getResource())) {
            $this->connection->initConnect();
        }

        return $this->connection;
    }

    public function bootstrap()
    {
        // TODO: Implement bootstrap() method.
    }

    public function terminate()
    {
        $this->connection->closeConnect();
    }

    public function __construct(ConnectionInterface $connection = null)
    {
        if ($connection) {
            $this->setConnection($connection);
        }
    }

    public function fetch($sql, array $params = [], $fetchClass = "")
    {
        return $this->getConnection()->fetch($sql, $params, $fetchClass, [$this->getConnection()]);
    }

    public function fetchAll($sql, array $params = [], $fetchClass = "")
    {
        return $this->getConnection()->fetchAll($sql, $params, $fetchClass, [$this->getConnection()]);
    }

    public function execute($sql, array $params = [])
    {
        return $this->getConnection()->execute($sql, $params);
    }
}