<?php

use App\Controllers\IndexController;


return [
    '/' => [
        'controller' => IndexController::class, 'method' => 'index', 'args' => [
            'test' => 'hello world'
        ]
    ],

    '/regex/([0-9]+)/edit/([a-z]+).html' => [
        'controller' => IndexController::class, 'method' => 'regex'
    ],

    '/db' => [
        'controller' => IndexController::class, 'method' => 'db'
    ]
];