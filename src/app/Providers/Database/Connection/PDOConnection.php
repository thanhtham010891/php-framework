<?php

namespace App\Providers\Database\Connection;

use System\Contract\Database\ConnectionInterface;
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
     * @var string
     */
    private $currentConnectionName = 'default';

    /**
     * @var array
     */
    private $connection = [
        'default' => null
    ];

    public function __construct($settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param string $name
     * @throws ConnectionException
     */
    public function openConnect($name = 'default')
    {
        if (empty($this->connection[$name])) {

            if (!empty($this->settings['charset'])) {
                $charset = 'charset=' . $this->settings['charset'];
            } else {
                $charset = '';
            }

            $this->settings = array_replace($this->settings, [PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING]);

            try {

                $this->connection[$name] = new PDO(
                    'mysql:host=' . $this->settings['hostname'] . ';dbname=' . $this->settings['db_name'] . ';' . $charset,
                    $this->settings['username'], $this->settings['password'], $this->settings['options']
                );

                $this->currentConnectionName = $name;

            } catch (PDOException $e) {
                throw new ConnectionException('Open connect is lost: ' . $e->getMessage());
            }
        }
    }

    /**
     * @param string $name
     *
     * The connection remains active for the lifetime of that PDO object.
     * To close the connection, you need to destroy the object by ensuring that all remaining references
     * to it are deleted--you do this by assigning NULL to the variable that holds the object.
     * If you don't do this explicitly, PHP will automatically close the connection when your script ends.
     */
    public function closeConnect($name = 'default')
    {
        $this->connection[$name] = null;
    }

    /**
     * @param string $name
     * @throws ConnectionException
     */
    public function reConnect($name = 'default')
    {
        $this->closeConnect($name);

        $this->openConnect($name);
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
     * @throws ConnectionException
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
     * @throws ConnectionException
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
     * @throws ConnectionException
     */
    public function execute($sql, array $params = [])
    {
        /**
         * @var PDO $connection
         */
        $connection = $this->connection[$this->currentConnectionName];

        $stmt = $connection->prepare($sql);

        if (!$stmt->execute($params)) {
            throw new ConnectionException('Execute query: ' . json_encode($stmt->errorInfo()));
        }

        return $stmt;
    }
}