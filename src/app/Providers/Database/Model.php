<?php

namespace App\Providers\Database;

use System\Contract\Database\QueryBuilderInterface;
use System\Contract\Database\ModelInterface;
use System\Contract\Database\DatabaseInterface;
use System\BaseException;

abstract class Model implements ModelInterface
{
    /**
     * Table identity
     *
     * @var array
     */
    protected $identity = [

        /**
         * Table name
         */
        'table' => '__NONE__',

        /**
         * Primary key of table
         */
        'primaryKey' => 'id',

        /**
         * Set acceptance fill for create or update
         */
        'columns' => []
    ];

    private $executeDBState = [

        /**
         * Slave group
         */
        'App\Providers\Database\Model::fetchOne' => 'slave',
        'App\Providers\Database\Model::fetchAll' => 'slave',

        /**
         * Master group
         */
        'App\Providers\Database\Model::create' => 'master',
        'App\Providers\Database\Model::update' => 'master',
        'App\Providers\Database\Model::delete' => 'master',
        'App\Providers\Database\Model::truncate' => 'master',
    ];

    /**
     * @var QueryBuilderInterface
     */
    protected $builder;

    /**
     * @var DatabaseInterface
     */
    private static $database;

    /**
     * @param DatabaseInterface $database
     */
    final public static function registerGlobals(DatabaseInterface $database)
    {
        self::$database = $database;
    }

    /**
     * @var string $method
     *
     * @return DatabaseInterface
     */
    final public function getDatabase($method)
    {
        $db = self::$database;

        if (!empty($this->executeDBState[$method])) {
            $db->getConnection($this->executeDBState[$method]);
        }

        return $db;
    }

    /**
     * @param QueryBuilderInterface $builder
     */
    final public function setQueryBuilder(QueryBuilderInterface $builder)
    {
        $this->builder = $builder;

        $this->builder->clearQueryResource();

        $this->builder->table($this->getTable());
    }

    /**
     * @return QueryBuilderInterface
     */
    final public function getQueryBuilder()
    {
        return $this->builder;
    }

    /**
     * @return string
     */
    final public function getTable()
    {
        return trim($this->identity['table'], '`');
    }

    /**
     * @return string
     */
    final public function getPrimaryKey()
    {
        if (empty($this->identity['primaryKey'])) {
            $this->identity['primaryKey'] = 'id';
        }

        return trim($this->identity['primaryKey'], '`');
    }

    /**
     * @return array
     */
    final public function getColumns()
    {
        if (empty($this->identity['columns'])) {
            $this->identity['columns'] = [];
        }

        return $this->identity['columns'];
    }

    /**
     * @param array $data
     * @return bool
     * @throws BaseException
     */
    final public function create(array $data)
    {
        list($fields, $values, $alias) = $this->_parseFieldsValue($data);

        return $this->getDatabase(__METHOD__)->execute(
            $this->getQueryBuilder()->buildQueryCreate($fields, $alias), $values
        );
    }

    /**
     * @param array $data
     * @return bool
     * @throws BaseException
     */
    final public function update(array $data)
    {
        list($fields, $values, $alias) = $this->_parseFieldsValue($data);

        return $this->getDatabase(__METHOD__)->execute(
            $this->getQueryBuilder()->buildQueryUpdate($fields, $alias), $values
        );
    }

    /**
     * If Query builder have no condition -> delete will be same at truncate method
     *
     * @return bool
     */
    final public function delete()
    {
        return $this->getDatabase(__METHOD__)->execute(
            $this->getQueryBuilder()->buildQueryDelete()
        );
    }

    /**
     * @return bool
     */
    final public function truncate()
    {
        return $this->getDatabase(__METHOD__)->execute(
            $this->getQueryBuilder()->buildQueryTruncate()
        );
    }

    /**
     * @param array $args
     * @return \Object
     */
    final public function fetchOne(array $args = [])
    {
        return $this->getDatabase(__METHOD__)->fetchOne(
            $this->getQueryBuilder()->buildExecuteNoneQuery(), $args, get_class($this)
        );
    }

    /**
     * @param array $args
     * @return array|mixed
     */
    final public function fetchAll(array $args = [])
    {
        return $this->getDatabase(__METHOD__)->fetchAll(
            $this->getQueryBuilder()->buildExecuteNoneQuery(), $args, get_class($this)
        );
    }

    /**
     * @param $id
     * @return bool|\Object
     */
    final public function findById($id)
    {
        $this->getQueryBuilder()->whereEqual($this->getPrimaryKey(), '?');

        return $this->fetchOne([$id]);
    }

    /**
     * @param array $data
     * @return array
     * @throws BaseException
     */
    private function _parseFieldsValue(array $data)
    {
        $fields = $values = $alias = [];

        foreach ($data as $key => $item) {

            if (in_array($key, $this->getColumns())) {
                array_push($fields, $key);
                array_push($alias, '?');
                array_push($values, $item);
            }
        }

        if (empty($fields)) {
            throw new BaseException(
                'Database > model: table[' . $this->getTable() . '] have no columns for update or create'
            );
        }

        return [$fields, $values, $alias];
    }
}