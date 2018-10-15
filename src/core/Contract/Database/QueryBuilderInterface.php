<?php

namespace System\Contract\Database;

use System\Contract\ServiceInterface;

interface QueryBuilderInterface extends ServiceInterface
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

    /**
     * @return string
     */
    public function getQueryJoins();

    /**
     * @return string
     */
    public function getQueryLimit();

    /**
     * @return string
     */
    public function getQueryOrderBy();

    /**
     * @return string
     */
    public function getQueryGroupBy();

    /**
     * @return string
     */
    public function getQueryUnion();

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
     * @param $value
     * @param bool $and
     * @return $this
     */
    public function whereRLike($field, $value, $and = true);

    /**
     * @param $field
     * @param $value
     * @param bool $and
     * @return $this
     */
    public function whereRNotLike($field, $value, $and = true);

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
    public function whereMoreOrEqual($field, $value, $and = true);

    /**
     * @param $field
     * @param string $value
     * @param bool $and
     * @return $this
     */
    public function whereLessOrEqual($field, $value, $and = true);

    /**
     * @param string|QueryBuilderInterface $table
     * @param array|QueryBuilderInterface $statement
     * @return $this
     */
    public function innerJoin($table, QueryBuilderInterface $statement);

    /**
     * @param string|QueryBuilderInterface $table
     * @param QueryBuilderInterface $statement
     * @return $this
     */
    public function leftJoin($table, QueryBuilderInterface $statement);

    /**
     * @param string|QueryBuilderInterface $table
     * @param QueryBuilderInterface $statement
     * @return $this
     */
    public function rightJoin($table, QueryBuilderInterface $statement);

    /**
     * @param string $fields
     * @param string $sort
     * @return $this
     */
    public function orderBy($fields, $sort = 'ASC');

    /**
     * @param $fields
     * @return $this
     */
    public function groupBy($fields);

    /**
     * @param QueryBuilderInterface $statement
     * @return $this
     */
    public function having(QueryBuilderInterface $statement);

    /**
     * @param $limit
     * @param $offset
     * @return $this
     */
    public function limit($limit, $offset = 0);

    /**
     * @param QueryBuilderInterface $statement
     * @return $this
     */
    public function union(QueryBuilderInterface $statement);
}