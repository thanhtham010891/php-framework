<?php

namespace App\Providers\Database;

use App\Core\Contract\Database\ConnectionInterface;
use App\Exceptions\ApplicationException;
use PDO;

class PDOConnection implements ConnectionInterface
{
    private $settings;

    /**
     * @var PDO
     */
    private $connection;

    public function __construct($settings)
    {
        $this->settings = $settings;
    }

    public function initConnect()
    {
        if (empty($this->connection)) {
            if (!empty($this->settings['charset'])) {
                $charset = 'charset=' . $this->settings['charset'];
            } else {
                $charset = '';
            }

            $this->settings = array_replace($this->settings, [PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING]);

            $this->connection = new PDO(
                'mysql:host=' . $this->settings['hostname'] . ';dbname=' . $this->settings['db_name'] . ';' . $charset,
                $this->settings['username'], $this->settings['password'], $this->settings['options']
            );
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


    /**
     * @param string $sql
     * @param array $params
     * @param string $fetchClass
     * @param array $fetchClassArgs
     * @return mixed
     * @throws ApplicationException
     */
    public function fetch($sql, array $params = [], $fetchClass = "", $fetchClassArgs = [])
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
     * @throws ApplicationException
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
     * @throws ApplicationException
     */
    public function execute($sql, array $params = [])
    {
        $stmt = $this->getResource()->prepare($sql);

        if (!$stmt->execute($params)) {
            throw new ApplicationException(json_encode($stmt->errorInfo()));
        }

        return $stmt;
    }
}