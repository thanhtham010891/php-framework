<?php

use System\Contract\Http\RequestInterface;
use System\Contract\Http\ResponseInterface;
use System\Contract\Http\RouteCollectionInterface;

use System\Contract\Database\DatabaseInterface;
use System\Contract\Database\QueryBuilderInterface;

use System\BaseException;

return [

    RequestInterface::class => new \App\Providers\Http\Request(),
    RouteCollectionInterface::class => new \App\Providers\Http\RouteCollection(),
    ResponseInterface::class => new \App\Providers\Http\Response(),

    DatabaseInterface::class => function ($settings) {
        return new App\Providers\Database\Database(
            new \App\Providers\Database\Connection\PDOConnection($settings['models']['connection'])
        );
    },

    /**
     * Support for Mysql now...
     */
    QueryBuilderInterface::class => function ($settings) {

        if (strtolower($settings['models']['driver']) === 'mysql') {
            return new \App\Providers\Database\Builder\MysqlQueryBuilder();
        }

        throw new BaseException('Database driver not found!');
    }
];