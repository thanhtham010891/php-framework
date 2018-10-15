<?php

namespace System\Contract\Database;

interface ModelInterface
{
    /**
     * @var string $method
     *
     * @return DatabaseInterface
     */
    public function getDatabase($method);

    /**
     * @return string
     */
    public function getTableName();

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
     * @return null|\object
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