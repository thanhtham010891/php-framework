<?php

namespace App\Providers\Database\Builder;

use App\Core\Contract\Database\QueryBuilderInterface;
use App\Exceptions\ApplicationException;

class MysqlQueryBuilder implements QueryBuilderInterface
{
    /**
     * @var array
     */
    private $queryFormat = [
        'none_execute_query' => 'SELECT %fields% FROM %table% %join_conditions% WHERE %conditions% %order_by% %limit%',
        'create' => 'INSERT INTO %table% (%fields%) VALUES(%values%)',
        'update' => 'UPDATE %table% SET %fields_values% WHERE %conditions%',
        'delete' => 'DELETE FROM %table% WHERE %conditions%',
        'truncate' => 'TRUNCATE %table%',
    ];

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
            'limit' => '',
            'order_by'
        ];

        return $this;
    }

    /**
     * @return mixed
     * @throws ApplicationException
     */
    public function getTable()
    {
        if (empty($this->queryResource['from'])) {
            throw new ApplicationException('MysqlQueryBuilder: table is empty');
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
     * @param array $fields
     * @param array $values
     * @return string
     * @throws ApplicationException
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
     * @throws ApplicationException
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
     * @throws ApplicationException
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
     * @throws ApplicationException
     */
    public function buildQueryTruncate()
    {
        return strtr($this->queryFormat['truncate'], [
            '%table%' => $this->getTable(),
        ]);
    }

    /**
     * @return string
     * @throws ApplicationException
     */
    public function buildExecuteNoneQuery()
    {
        return strtr($this->queryFormat['none_execute_query'], [
            '%fields%' => $this->queryResource['select'],
            '%table%' => $this->getTable(),
            '%join_conditions%' => $this->_getJoins(),
            '%conditions%' => $this->getQueryConditions(),
            '%order_by%' => '',
            '%limit%' => $this->_getLimit()
        ]);
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
     * @throws ApplicationException
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
     * @throws ApplicationException
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
     * @throws ApplicationException
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
     * @throws ApplicationException
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
     * @throws ApplicationException
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
     * @throws ApplicationException
     */
    public function whereNotLike($field, $value, $and = true)
    {
        $this->_queryConditions($field, $value, 'NOT LIKE', $and ? 'AND' : 'OR');

        return $this;
    }

    /**
     * @param $field
     * @param string $value
     * @param bool $and
     * @return $this|QueryBuilderInterface
     * @throws ApplicationException
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
     * @throws ApplicationException
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
     * @throws ApplicationException
     */
    public function whereMoreOrEqualThan($field, $value, $and = true)
    {
        $this->_queryConditions($field, $value, '>=', $and ? 'AND' : 'OR');

        return $this;
    }

    /**
     * @param $field
     * @param string $value
     * @param bool $and
     * @return $this|QueryBuilderInterface
     * @throws ApplicationException
     */
    public function whereLessOrEqualThan($field, $value, $and = true)
    {
        $this->_queryConditions($field, $value, '<=', $and ? 'AND' : 'OR');

        return $this;
    }

    /**
     * @param QueryBuilderInterface|string $table
     * @param QueryBuilderInterface $conditions
     * @return $this|QueryBuilderInterface
     */
    public function innerJoin($table, QueryBuilderInterface $conditions)
    {
        $this->_queryJoins($table, $conditions, 'INNER');

        return $this;
    }

    /**
     * @param $limit
     * @param int $offset
     * @return $this|QueryBuilderInterface
     */
    public function limit($limit, $offset = 0)
    {
        $this->queryResource['limit'] = compact($limit, $offset);

        return $this;
    }

    /**
     * @param array $fields
     * @return string
     * @throws ApplicationException
     */
    private function _fieldsFormatToString(array $fields)
    {
        return $this->getTable() . '.`' . implode('`, ' . $this->getTable() . '.`', $fields) . '`';
    }

    /**
     * @param array $fields
     * @return array
     * @throws ApplicationException
     */
    private function _fieldsFormatToArray(array $fields)
    {
        foreach ($fields as &$item) {
            $item = $this->getTable() . '.`' . $item . '`';
        }

        return $fields;
    }

    private function _getJoins()
    {
        return implode(' ', $this->queryResource['join']);
    }

    private function _queryJoins($table, QueryBuilderInterface $conditions, $leftInRight)
    {
        if ($table instanceof QueryBuilderInterface) {

            $joinTable = '(' . $table->buildExecuteNoneQuery() . ') AS ' . $table->getTable();

        } else {

            $joinTable = '`' . trim($table, '`') . '`';
        }

        $this->queryResource['join'][] = $leftInRight .
            ' JOIN ' . $joinTable . ' ON (' . $conditions->getQueryConditions() . ')';
    }

    /**
     * @param string $field
     * @param string $value
     * @param string $operator
     * @param string $andOr
     * @return string
     * @throws ApplicationException
     */
    private function _queryConditions($field, $value, $operator, $andOr)
    {
        return $this->queryResource['where'][] = $andOr . ' ' . $this->_fieldsFormatToString([$field]) .
            ' ' . $operator . ' ' . trim($value);
    }

    private function _getLimit()
    {
        if (empty($this->queryResource['limit'])) {
            return '';
        }

        if (empty($this->queryResource['limit']['offset'])) {
            return 'LIMIT ' . $this->queryResource['limit']['limit'];
        }

        return 'LIMIT ' . $this->queryResource['limit']['offset'] . ', ' . $this->queryResource['limit']['limit'];
    }
}