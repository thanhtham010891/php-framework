<?php

use App\Providers\Database\Database;
use App\Core\Contract\Database\DatabaseInterface;

use App\Providers\Database\Connection\PDOConnection;

use App\Core\Contract\Database\QueryBuilderInterface;
use App\Providers\Database\Builder\MysqlQueryBuilder;
use App\Exceptions\ApplicationException;

return [

    DatabaseInterface::class => function ($settings) {
        return new Database(new PDOConnection($settings['models']['connection']));
    },

    /**
     * Support for Mysql now...
     */
    QueryBuilderInterface::class => function ($settings) {

        if (strtolower($settings['models']['driver']) === 'mysql') {
            return new MysqlQueryBuilder();
        }

        throw new ApplicationException('Database driver not found!');
    }
];