<?php

use System\Contract\Http\RequestInterface;
use System\Contract\Http\ResponseInterface;
use System\Contract\Http\RouteInterface;

use System\Contract\View\ViewManagerInterface;

use System\Contract\Session\SessionInterface;

use System\Contract\Database\DatabaseInterface;
use System\Contract\Database\QueryBuilderInterface;

use System\BaseException;

return [

    RequestInterface::class => function () {
        return new \App\Providers\Http\Request();
    },

    RouteInterface::class => function () {
        return new \App\Providers\Http\Route();
    },

    SessionInterface::class => function () {
        return new \App\Providers\Session\Manager();
    },

    ResponseInterface::class => function () {
        return new \App\Providers\Http\Response();
    },

    ViewManagerInterface::class => function ($settings) {
        return new App\Providers\View\Manager($settings['views']);
    },

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