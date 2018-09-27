<?php

namespace App\Providers\Database\Builder;

use System\Contract\Database\QueryBuilderInterface;
use System\BaseException;

class MysqlQueryBuilder implements QueryBuilderInterface
{
    /**
     * @var array
     */
    private $queryFormat = [
        'none_execute_query' => 'SELECT %fields% FROM %table% %join_conditions% WHERE %conditions% %group_by% %order_by% %limit% %union%',
        'create' => 'INSERT INTO %table% (%fields%) VALUES(%values%)',
        'update' => 'UPDATE %table% SET %fields_values% WHERE %conditions%',
        'delete' => 'DELETE FROM %table% WHERE %conditions%',
        'truncate' => 'TRUNCATE %table%',
    ];

    public function bootstrap()
    {
    }

    public function terminate()
    {
    }

    public function replicate()
    {
        return true;
    }

    /**
     * @var array
     */
    private $queryResource;

    public function clearQueryResource()
    {
        $this->queryResource = [
            'select' => '*',
            'from' => '',
            'join' => [],
            'where' => [],
            'order_by' => [],
            'group_by' => [],
            'having' => [],
            'union' => [],
            'limit' => '',
        ];

        return $this;
    }

    /**
     * @return string
     * @throws BaseException
     */
    public function getTable()
    {
        if (empty($this->queryResource['from'])) {
            throw new BaseException('MysqlQueryBuilder: table is empty');
        }

        return $this->queryResource['from'];
    }

    /**
     * @param $table
     * @return $this
     */
    public function table($table)
    {
        $this->queryResource['from'] = '`' . trim($table, '`') . '`';

        return $this;
    }

    /**
     * @return string
     */
    public function getQueryConditions()
    {
        if (empty($this->queryResource['where'])) {
            return '1';
        }

        return '1 ' . implode(' ', $this->queryResource['where']);
    }

    /**
     * @return string
     */
    public function getQueryJoins()
    {
        return implode(' ', $this->queryResource['join']);
    }

    /**
     * @return string
     */
    public function getQueryLimit()
    {
        if (empty($this->queryResource['limit'])) {
            return '';
        }

        if (empty($this->queryResource['limit']['offset'])) {
            return 'LIMIT ' . $this->queryResource['limit']['limit'];
        }

        return 'LIMIT ' . $this->queryResource['limit']['offset'] . ', ' . $this->queryResource['limit']['limit'];
    }

    /**
     * @return string
     */
    public function getQueryOrderBy()
    {
        if (empty($this->queryResource['order_by'])) {
            return '';
        }

        return 'ORDER BY ' . implode(', ', $this->queryResource['order_by']);
    }

    /**
     * @return string
     */
    public function getQueryGroupBy()
    {
        if (empty($this->queryResource['group_by'])) {
            return '';
        }

        $having = '';

        if (!empty($this->queryResource['having'])) {
            $having = 'HAVING ' . implode(' ', $this->queryResource['having']);
        }

        return 'GROUP BY ' . implode(', ', $this->queryResource['group_by']) . ' ' . $having;
    }

    public function getQueryUnion()
    {
        if (empty($this->queryResource['union'])) {
            return '';
        }

        return implode(' ', $this->queryResource['union']);
    }

    /**
     * @param array $fields
     * @param array $values
     * @return string
     * @throws BaseException
     */
    public function buildQueryCreate(array $fields, array $values)
    {
        $fields = $this->_fieldsFormatToString($fields);

        return strtr(
            $this->queryFormat['create'], [
            '%table%' => $this->getTable(), '%fields%' => $fields, '%values%' => implode(', ', $values)
        ]);

    }

    /**
     * @param array $fields
     * @param array $values
     * @return string
     * @throws BaseException
     */
    public function buildQueryUpdate(array $fields, array $values)
    {
        $fields_values = '';

        foreach (array_combine($this->_fieldsFormatToArray($fields), $values) as $key => $item) {
            $fields_values .= $key . ' = ' . $item . ', ';
        }

        return strtr($this->queryFormat['update'], [
            '%table%' => $this->getTable(),
            '%fields_values%' => trim($fields_values, ', '),
            '%conditions%' => $this->getQueryConditions(),
        ]);
    }

    /**
     * @return string
     * @throws BaseException
     */
    public function buildQueryDelete()
    {
        return strtr($this->queryFormat['delete'], [
            '%table%' => $this->getTable(),
            '%conditions%' => $this->getQueryConditions()
        ]);
    }

    /**
     * @return string
     * @throws BaseException
     */
    public function buildQueryTruncate()
    {
        return strtr($this->queryFormat['truncate'], [
            '%table%' => $this->getTable(),
        ]);
    }

    /**
     * @return string
     * @throws BaseException
     */
    public function buildExecuteNoneQuery()
    {
        return trim(strtr($this->queryFormat['none_execute_query'], [
            '%fields%' => $this->queryResource['select'],
            '%table%' => $this->getTable(),
            '%join_conditions%' => $this->getQueryJoins(),
            '%conditions%' => $this->getQueryConditions(),
            '%group_by%' => $this->getQueryGroupBy(),
            '%order_by%' => $this->getQueryOrderBy(),
            '%limit%' => $this->getQueryLimit(),
            '%union%' => $this->getQueryUnion(),
        ]));
    }

    /**
     * @param array $fields
     * @return $this|QueryBuilderInterface
     */
    public function select(array $fields = [])
    {
        if (!empty($fields)) {
            $this->queryResource['select'] = implode(', ', $fields);
        }

        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @param bool $and
     * @return $this|QueryBuilderInterface
     * @throws BaseException
     */
    public function whereEqual($field, $value, $and = true)
    {
        $this->_queryConditions($field, $value, '=', $and ? 'AND' : 'OR');

        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @param bool $and
     * @return $this|QueryBuilderInterface
     * @throws BaseException
     */
    public function whereNotEqual($field, $value, $and = true)
    {
        $this->_queryConditions($field, $value, '<>', $and ? 'AND' : 'OR');

        return $this;
    }

    /**
     * @param $field
     * @param array $value
     * @param bool $and
     * @return $this|QueryBuilderInterface
     * @throws BaseException
     */
    public function whereIn($field, array $value, $and = true)
    {
        $this->_queryConditions($field, '(' . implode(', ', $value) . ')', 'IN', $and ? 'AND' : 'OR');

        return $this;
    }

    /**
     * @param $field
     * @param array $value
     * @param bool $and
     * @return $this|QueryBuilderInterface
     * @throws BaseException
     */
    public function whereNotIn($field, array $value, $and = true)
    {
        $this->_queryConditions($field, '(' . implode(', ', $value) . ')', 'NOT IN', $and ? 'AND' : 'OR');

        return $this;
    }

    /**
     * @param $field
     * @param string $value
     * @param bool $and
     * @return $this|QueryBuilderInterface
     * @throws BaseException
     */
    public function whereLike($field, $value, $and = true)
    {
        $this->_queryConditions($field, $value, 'LIKE', $and ? 'AND' : 'OR');

        return $this;
    }

    /**
     * @param $field
     * @param string $value
     * @param bool $and
     * @return $this|QueryBuilderInterface
     * @throws BaseException
     */
    public function whereNotLike($field, $value, $and = true)
    {
        $this->_queryConditions($field, $value, 'NOT LIKE', $and ? 'AND' : 'OR');

        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @param bool $and
     * @return $this|QueryBuilderInterface
     * @throws BaseException
     */
    public function whereRLike($field, $value, $and = true)
    {
        $this->_queryConditions($field, $value, 'RLIKE', $and ? 'AND' : 'OR');

        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @param bool $and
     * @return $this|QueryBuilderInterface
     * @throws BaseException
     */
    public function whereRNotLike($field, $value, $and = true)
    {
        $this->_queryConditions($field, $value, 'NOT RLIKE', $and ? 'AND' : 'OR');

        return $this;
    }

    /**
     * @param $field
     * @param string $value
     * @param bool $and
     * @return $this|QueryBuilderInterface
     * @throws BaseException
     */
    public function whereMoreThan($field, $value, $and = true)
    {
        $this->_queryConditions($field, $value, '>', $and ? 'AND' : 'OR');

        return $this;
    }

    /**
     * @param $field
     * @param string $value
     * @param bool $and
     * @return $this|QueryBuilderInterface
     * @throws BaseException
     */
    public function whereLessThan($field, $value, $and = true)
    {
        $this->_queryConditions($field, $value, '<', $and ? 'AND' : 'OR');

        return $this;
    }

    /**
     * @param $field
     * @param string $value
     * @param bool $and
     * @return $this|QueryBuilderInterface
     * @throws BaseException
     */
    public function whereMoreOrEqual($field, $value, $and = true)
    {
        $this->_queryConditions($field, $value, '>=', $and ? 'AND' : 'OR');

        return $this;
    }

    /**
     * @param $field
     * @param string $value
     * @param bool $and
     * @return $this|QueryBuilderInterface
     * @throws BaseException
     */
    public function whereLessOrEqual($field, $value, $and = true)
    {
        $this->_queryConditions($field, $value, '<=', $and ? 'AND' : 'OR');

        return $this;
    }

    /**
     * @param QueryBuilderInterface|string $table
     * @param QueryBuilderInterface $statement
     * @return $this|QueryBuilderInterface
     */
    public function innerJoin($table, QueryBuilderInterface $statement)
    {
        $this->_queryJoins($table, $statement, 'INNER');

        return $this;
    }

    /**
     * @param QueryBuilderInterface|string $table
     * @param QueryBuilderInterface $statement
     * @return $this|QueryBuilderInterface
     */
    public function leftJoin($table, QueryBuilderInterface $statement)
    {
        $this->_queryJoins($table, $statement, 'LEFT');

        return $this;
    }

    /**
     * @param QueryBuilderInterface|string $table
     * @param QueryBuilderInterface $statement
     * @return $this|QueryBuilderInterface
     */
    public function rightJoin($table, QueryBuilderInterface $statement)
    {
        $this->_queryJoins($table, $statement, 'RIGHT');

        return $this;
    }

    /**
     * @param string $fields
     * @param string $sort
     * @return $this|QueryBuilderInterface
     * @throws BaseException
     */
    public function orderBy($fields, $sort = 'DESC')
    {
        $this->queryResource['order_by'][] = $this->_fieldsFormatToString([$fields]) . ' ' . trim($sort);

        return $this;
    }

    /**
     * @param $fields
     * @return $this|QueryBuilderInterface
     * @throws BaseException
     */
    public function groupBy($fields)
    {
        $this->queryResource['group_by'][] = $this->_fieldsFormatToString([$fields]);

        return $this;
    }

    /**
     * @param QueryBuilderInterface $statement
     * @return $this|QueryBuilderInterface
     */
    public function having(QueryBuilderInterface $statement)
    {
        $this->queryResource['having'][] = $statement->getQueryConditions();

        return $this;
    }

    /**
     * @param $limit
     * @param int $offset
     * @return $this|QueryBuilderInterface
     */
    public function limit($limit, $offset = 0)
    {
        $this->queryResource['limit'] = ['limit' => $limit, 'offset' => $offset];

        return $this;
    }

    /**
     * @param QueryBuilderInterface $statement
     * @return QueryBuilderInterface|$this
     */
    public function union(QueryBuilderInterface $statement)
    {
        $this->queryResource['union'][] = 'UNION (' . $statement->buildExecuteNoneQuery() . ')';

        return $this;
    }

    /**
     * @param array $fields
     * @return string
     * @throws BaseException
     */
    private function _fieldsFormatToString(array $fields)
    {
        return $this->getTable() . '.`' . implode('`, ' . $this->getTable() . '.`', $fields) . '`';
    }

    /**
     * @param array $fields
     * @return array
     * @throws BaseException
     */
    private function _fieldsFormatToArray(array $fields)
    {
        foreach ($fields as &$item) {
            $item = $this->getTable() . '.`' . $item . '`';
        }

        return $fields;
    }

    private function _queryJoins($table, QueryBuilderInterface $statement, $leftInRight)
    {
        if ($table instanceof QueryBuilderInterface) {

            $joinTable = '(' . $table->buildExecuteNoneQuery() . ') AS ' . $table->getTable();

        } else {

            $joinTable = '`' . trim($table, '`') . '`';
        }

        $this->queryResource['join'][] = $leftInRight .
            ' JOIN ' . $joinTable . ' ON (' . $statement->getQueryConditions() . ')';
    }

    /**
     * @param string $field
     * @param string $value
     * @param string $operator
     * @param string $andOr
     * @return string
     * @throws BaseException
     */
    private function _queryConditions($field, $value, $operator, $andOr)
    {
        return $this->queryResource['where'][] = $andOr . ' ' . $this->_fieldsFormatToString([$field]) .
            ' ' . $operator . ' ' . trim($value);
    }
}