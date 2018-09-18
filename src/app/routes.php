<?php

use App\Controllers\IndexController;


return [
    '/' => [
        'controller' => IndexController::class, 'method' => 'index', 'args' => []
    ],
    '/test.html' => view_path() . 'index.html'
];