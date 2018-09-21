<?php

namespace App\Core\Contract\Database;

interface QueryBuilderInterface
{

    /**
     * @return $this
     */
    public function clearQueryResource();

    /**
     * @return string
     */
    public function getTable();

    /**
     * @param string $table
     * @return $this
     */
    public function table($table);

    /**
     * @return string
     */
    public function getQueryConditions();


    // EXECUTE QUERY

    /**
     * @param array $fields
     * @param array $values
     * @return string
     */
    public function buildQueryCreate(array $fields, array $values);

    /**
     * @param array $fields
     * @param array $values
     * @return mixed
     */
    public function buildQueryUpdate(array $fields, array $values);

    /**
     * @return string
     */
    public function buildQueryDelete();

    /**
     * @return string
     */
    public function buildQueryTruncate();

    // EXECUTE NONE QUERY

    /**
     * @return string
     */
    public function buildExecuteNoneQuery();

    /**
     * @param array $fields
     * @return $this
     */
    public function select(array $fields = []);

    /**
     * @param $field
     * @param string $value
     * @param bool $and
     * @return $this
     */
    public function whereEqual($field, $value, $and = true);

    /**
     * @param $field
     * @param string $value
     * @param bool $and
     * @return $this
     */
    public function whereNotEqual($field, $value, $and = true);

    /**
     * @param $field
     * @param array $value
     * @param bool $and
     * @return $this
     */
    public function whereIn($field, array $value, $and = true);

    /**
     * @param $field
     * @param array $value
     * @param bool $and
     * @return $this
     */
    public function whereNotIn($field, array $value, $and = true);

    /**
     * @param $field
     * @param string $value
     * @param bool $and
     * @return $this
     */
    public function whereLike($field, $value, $and = true);

    /**
     * @param $field
     * @param string $value
     * @param bool $and
     * @return $this
     */
    public function whereNotLike($field, $value, $and = true);

    /**
     * @param $field
     * @param string $value
     * @param bool $and
     * @return $this
     */
    public function whereMoreThan($field, $value, $and = true);

    /**
     * @param $field
     * @param string $value
     * @param bool $and
     * @return $this
     */
    public function whereLessThan($field, $value, $and = true);

    /**
     * @param $field
     * @param string $value
     * @param bool $and
     * @return $this
     */
    public function whereMoreOrEqualThan($field, $value, $and = true);

    /**
     * @param $field
     * @param string $value
     * @param bool $and
     * @return $this
     */
    public function whereLessOrEqualThan($field, $value, $and = true);

    /**
     * @param string|QueryBuilderInterface $table
     * @param array|QueryBuilderInterface $conditions
     * @return $this
     */
    public function innerJoin($table, QueryBuilderInterface $conditions);

    /**
     * @param $limit
     * @param $offset
     * @return $this
     */
    public function limit($limit, $offset = 0);
}