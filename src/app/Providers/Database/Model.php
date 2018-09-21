<?php

namespace App\Providers\Database;

use App\Core\Contract\Database\QueryBuilderInterface;
use App\Core\Contract\Database\ModelInterface;
use App\Core\Contract\Database\DatabaseInterface;

abstract class Model implements ModelInterface
{
    /**
     * @var DatabaseInterface
     */
    private $database;

    /**
     * @var QueryBuilderInterface
     */
    private $builder;

    /**
     * @var string
     */
    protected $table = '__NONE__';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @param DatabaseInterface $database
     * @param QueryBuilderInterface $builder
     */
    final public function bootstrap(DatabaseInterface $database, QueryBuilderInterface $builder)
    {
        $this->database = $database;

        $this->builder = $builder;

        $this->builder->clearQueryResource();
    }

    /**
     * @return QueryBuilderInterface
     */
    public function getQueryBuilder()
    {
        $builder = clone $this->builder;

        $builder->clearQueryResource();

        return $builder;
    }

    /**
     * @param $id
     * @return Object
     */
    public function findById($id)
    {
        return $this->fetchOne(
            $this->getQueryBuilder()->whereEqual($this->primaryKey, '?'), [$id]
        );
    }

    /**
     * @param QueryBuilderInterface $builder
     * @param array $args
     * @return Object
     */
    public function fetchOne(QueryBuilderInterface $builder, $args = [])
    {
        $builder->table($this->table);

        return $this->database->fetchOne(
            $builder->buildExecuteNoneQuery(), $args, get_class($this)
        );
    }

    /**
     * @param QueryBuilderInterface $builder
     * @param array $args
     * @return array
     */
    public function fetchAll(QueryBuilderInterface $builder, $args = [])
    {
        $builder->table($this->table);

        return $this->database->fetchAll(
            $builder->buildExecuteNoneQuery(), $args, get_class($this)
        );
    }
}