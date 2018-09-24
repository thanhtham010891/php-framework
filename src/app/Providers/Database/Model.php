<?php

namespace App\Providers\Database;

use App\Core\Contract\Database\QueryBuilderInterface;
use App\Core\Contract\Database\ModelInterface;
use App\Core\Contract\Database\DatabaseInterface;
use App\Exceptions\ApplicationException;

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

    /**
     * @var DatabaseInterface
     */
    private $_database;

    /**
     * @var QueryBuilderInterface
     */
    private $_builder;

    /**
     * @param DatabaseInterface $database
     * @param QueryBuilderInterface $builder
     */
    final public function bootstrap(DatabaseInterface $database, QueryBuilderInterface $builder)
    {
        $this->_database = $database;

        $this->_builder = $builder;

        $this->_builder->clearQueryResource();

        $this->_builder->table($this->getTable());
    }

    /**
     * @return DatabaseInterface
     */
    final public function getDatabase()
    {
        return $this->_database;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return trim($this->identity['table'], '`');
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        return trim($this->identity['primaryKey'], '`');
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->identity['columns'];
    }

    /**
     * @return QueryBuilderInterface
     */
    final public function getQueryBuilder()
    {
        return $this->_builder;
    }

    /**
     * @return QueryBuilderInterface
     */
    final public function getNewQueryBuilder()
    {
        $builder = clone $this->getQueryBuilder();

        $builder->clearQueryResource();

        return $builder;
    }

    /**
     * @param array $data
     * @return bool
     * @throws ApplicationException
     */
    public function create(array $data)
    {
        list($fields, $values, $alias) = $this->_parseFieldsValue($data);

        return $this->getDatabase()->execute(
            $this->getQueryBuilder()->buildQueryCreate($fields, $alias), $values
        );
    }

    /**
     * @param array $data
     * @return bool
     * @throws ApplicationException
     */
    public function update(array $data)
    {
        list($fields, $values, $alias) = $this->_parseFieldsValue($data);

        return $this->getDatabase()->execute(
            $this->getQueryBuilder()->buildQueryUpdate($fields, $alias), $values
        );
    }

    /**
     * If Query builder have no condition -> delete will be same at truncate method
     *
     * @return bool
     */
    public function delete()
    {
        return $this->getDatabase()->execute(
            $this->getQueryBuilder()->buildQueryDelete()
        );
    }

    /**
     * @return bool
     */
    public function truncate()
    {
        return $this->getDatabase()->execute(
            $this->getQueryBuilder()->buildQueryTruncate()
        );
    }

    /**
     * @param array $args
     * @return Object
     */
    public function fetchOne(array $args = [])
    {
        return $this->getDatabase()->fetchOne(
            $this->getQueryBuilder()->buildExecuteNoneQuery(), $args, get_class($this)
        );
    }

    /**
     * @param array $args
     * @return array
     */
    public function fetchAll(array $args = [])
    {
        return $this->getDatabase()->fetchAll(
            $this->getQueryBuilder()->buildExecuteNoneQuery(), $args, get_class($this)
        );
    }

    /**
     * @param $id
     * @return Object
     */
    public function findById($id)
    {
        $this->getQueryBuilder()->whereEqual($this->getPrimaryKey(), '?');

        return $this->fetchOne([$id]);
    }

    /**
     * @param array $data
     * @return array
     * @throws ApplicationException
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
            throw new ApplicationException(
                'Database > model: table[' . $this->getTable() . '] have no columns for update or create'
            );
        }

        return [$fields, $values, $alias];
    }
}