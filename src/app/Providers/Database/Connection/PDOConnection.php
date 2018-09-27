<?php

namespace App\Providers\Database\Connection;

use System\Contract\Database\ConnectionInterface;
use System\BaseException;
use PDO;
use PDOException;

class PDOConnection implements ConnectionInterface
{
    /**
     * Connection settings
     *
     * @var array
     */
    private $settings;

    /**
     * @var PDO
     */
    private $connection;

    public function __construct($settings)
    {
        $this->settings = $settings;
    }

    /**
     * @throws BaseException
     */
    public function openConnect()
    {
        if (empty($this->connection)) {
            if (!empty($this->settings['charset'])) {
                $charset = 'charset=' . $this->settings['charset'];
            } else {
                $charset = '';
            }

            $this->settings = array_replace($this->settings, [PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING]);

            try {

                $this->connection = new PDO(
                    'mysql:host=' . $this->settings['hostname'] . ';dbname=' . $this->settings['db_name'] . ';' . $charset,
                    $this->settings['username'], $this->settings['password'], $this->settings['options']
                );

            } catch (PDOException $e) {
                throw new BaseException($e->getMessage());
            }
        }
    }

    /**
     * The connection remains active for the lifetime of that PDO object.
     * To close the connection, you need to destroy the object by ensuring that all remaining references
     * to it are deleted--you do this by assigning NULL to the variable that holds the object.
     * If you don't do this explicitly, PHP will automatically close the connection when your script ends.
     */
    public function closeConnect()
    {
        $this->connection = null;
    }

    /**
     * @return PDO
     */
    public function getResource()
    {
        return $this->connection;
    }

    public function getDatabaseName()
    {
        return trim($this->settings['db_name'], '`');
    }

    /**
     * @param string $sql
     * @param array $params
     * @param string $fetchClass
     * @param array $fetchClassArgs
     * @return mixed
     * @throws BaseException
     */
    public function fetchOne($sql, array $params = [], $fetchClass = "", $fetchClassArgs = [])
    {
        $stmt = $this->execute($sql, $params);

        if (!empty($fetchClass)) {
            $stmt->setFetchMode(PDO::FETCH_CLASS, $fetchClass, $fetchClassArgs);
        }

        return $stmt->fetch();
    }

    /**
     * @param string $sql
     * @param array $params
     * @param string $fetchClass
     * @param array $fetchClassArgs
     * @return array
     * @throws BaseException
     */
    public function fetchAll($sql, array $params = [], $fetchClass = "", $fetchClassArgs = [])
    {
        $stmt = $this->execute($sql, $params);

        if (!empty($fetchClass)) {
            $stmt->setFetchMode(PDO::FETCH_CLASS, $fetchClass, $fetchClassArgs);
        }

        return $stmt->fetchAll();
    }

    /**
     * @param string $sql
     * @param array $params
     * @return bool|\PDOStatement
     * @throws BaseException
     */
    public function execute($sql, array $params = [])
    {
        $stmt = $this->getResource()->prepare($sql);

        if (!$stmt->execute($params)) {
            throw new BaseException(json_encode($stmt->errorInfo()));
        }

        return $stmt;
    }
}