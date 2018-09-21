<?php

use App\Providers\Database\Database;
use App\Core\Contract\Database\DatabaseInterface;

use App\Providers\Database\Connection\PDOConnection;

use App\Core\Contract\Database\QueryBuilderInterface;
use App\Providers\Database\Builder\MysqlQueryBuilder;

return [

    DatabaseInterface::class => function ($settings) {
        return new Database(new PDOConnection($settings['models']['connection']));
    },

    /**
     * Support for Mysql now...
     */
    QueryBuilderInterface::class => function () {
        return new MysqlQueryBuilder();
    }
];