<?php

namespace App\Core\Contract\Database;

interface ModelInterface
{
    /**
     * Set database connection, $this->database should be private for restrict model directory access to connection
     *
     * @param DatabaseInterface $database
     * @param QueryBuilderInterface $builder
     * @return void
     */
    public function bootstrap(DatabaseInterface $database, QueryBuilderInterface $builder);

    /**
     * This function will clone to new QueryBuilder
     *
     * @return QueryBuilderInterface
     */
    public function getQueryBuilder();

    /**
     * Get One item by primaryKey
     *
     * @param $id
     * @return object|bool
     */
    public function findById($id);

    /**
     * Get one item
     *
     * @param QueryBuilderInterface $builder
     * @param array $args
     * @return mixed
     */
    public function fetchOne(QueryBuilderInterface $builder, $args = []);

    /**
     * Get all items
     *
     * @param QueryBuilderInterface $builder
     * @param array $args
     * @return mixed
     */
    public function fetchAll(QueryBuilderInterface $builder, $args = []);
}