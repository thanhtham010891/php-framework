<?php

namespace App\Providers\Database;

use System\Contract\Database\ConnectionInterface;
use System\Contract\Database\DatabaseInterface;
use System\BaseException;

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

    /**
     * @return ConnectionInterface
     * @throws BaseException
     */
    public function getConnection()
    {
        if (empty($this->connection)) {
            throw new BaseException('Database connection is empty');
        }

        if (empty($this->connection->getResource())) {
            $this->connection->openConnect();
        }

        return $this->connection;
    }

    /**
     * @return string
     * @throws BaseException
     */
    public function getDatabaseName()
    {
        return $this->getConnection()->getDatabaseName();
    }

    public function __construct(ConnectionInterface $connection)
    {
        $this->setConnection($connection);
    }

    public function bootstrap()
    {
    }

    /**
     * @throws BaseException
     */
    public function terminate()
    {
        $this->getConnection()->closeConnect();
    }

    public function replicate()
    {
        return false;
    }

    /**
     * @param string $sql
     * @param array $params
     * @param string $fetchClass
     * @return mixed|Object
     * @throws BaseException
     */
    public function fetchOne($sql, array $params = [], $fetchClass = "")
    {
        return $this->getConnection()->fetchOne($sql, $params, $fetchClass, []);
    }

    /**
     * @param string $sql
     * @param array $params
     * @param string $fetchClass
     * @return array
     * @throws BaseException
     */
    public function fetchAll($sql, array $params = [], $fetchClass = "")
    {
        return $this->getConnection()->fetchAll($sql, $params, $fetchClass, []);
    }

    /**
     * @param string $sql
     * @param array $params
     * @return bool
     * @throws BaseException
     */
    public function execute($sql, array $params = [])
    {
        return $this->getConnection()->execute($sql, $params);
    }
}