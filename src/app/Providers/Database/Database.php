<?php

namespace App\Providers\Database;

use App\Core\Contract\Database\ConnectionInterface;
use App\Core\Contract\Database\DatabaseInterface;
use App\Exceptions\ApplicationException;

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
     * @throws ApplicationException
     */
    public function getConnection()
    {
        if (empty($this->connection)) {
            throw new ApplicationException('Database connection is empty');
        }

        if (empty($this->connection->getResource())) {
            $this->connection->openConnect();
        }

        return $this->connection;
    }

    /**
     * @return string
     * @throws ApplicationException
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
     * @throws ApplicationException
     */
    public function terminate()
    {
        $this->getConnection()->closeConnect();
    }

    /**
     * @param string $sql
     * @param array $params
     * @param string $fetchClass
     * @return mixed|Object
     * @throws ApplicationException
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
     * @throws ApplicationException
     */
    public function fetchAll($sql, array $params = [], $fetchClass = "")
    {
        return $this->getConnection()->fetchAll($sql, $params, $fetchClass, []);
    }

    /**
     * @param string $sql
     * @param array $params
     * @return bool
     * @throws ApplicationException
     */
    public function execute($sql, array $params = [])
    {
        return $this->getConnection()->execute($sql, $params);
    }
}