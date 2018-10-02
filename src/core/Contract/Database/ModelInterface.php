<?php

namespace System\Contract\Database;

interface ModelInterface
{
    /**
     * Set database connection, QueryBuilder
     *
     * @param DatabaseInterface $database
     * @param QueryBuilderInterface $builder
     * @return void
     */
    public function bootstrap(DatabaseInterface $database, QueryBuilderInterface $builder);

    /**
     * @return DatabaseInterface
     */
    public function getDatabase();

    /**
     * @return string
     */
    public function getTable();

    /**
     * @return string
     */
    public function getPrimaryKey();

    /**
     * @return array
     */
    public function getColumns();

    /**
     * Get One item by primaryKey
     *
     * @param $id
     * @return object|bool
     */
    public function findById($id);

    /**
     * @param array $data
     * @return bool
     */
    public function create(array $data);

    /**
     * @param array $data
     * @return bool
     */
    public function update(array $data);

    /**
     * @return bool
     */
    public function delete();

    /**
     * @return bool
     */
    public function truncate();

    /**
     * Get one item
     *
     * @param array $args
     * @return mixed
     */
    public function fetchOne(array $args = []);

    /**
     * Get all items
     *
     * @param array $args
     * @return mixed
     */
    public function fetchAll(array $args = []);
}