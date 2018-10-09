<?php

use App\Controllers\IndexController;


$this->get('/', [
    'controller' => IndexController::class, 'method' => 'index'
]);

$this->get('/regex/([0-9]+)/edit/([a-z]+).html', [
    'controller' => IndexController::class, 'method' => 'index'
]);


$this->get('/db', [
    'controller' => IndexController::class, 'method' => 'db'
]);