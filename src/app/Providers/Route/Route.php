<?php

namespace App\Providers\Route;

use App\Controllers\src\IndexController;
use App\Core\Contract\RouteInterface;

class Route implements RouteInterface
{

    public function getResource()
    {
        return [
            '/' => [
                'controller' => IndexController::class, 'method' => 'index', 'args' => []
            ],
            '/post/([0-9]+)?' => [
                'controller' => IndexController::class, 'method' => 'index'
            ]
        ];
    }
}