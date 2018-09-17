<?php

namespace App\Providers\Route;

use App\Controllers\Api\V1;
use App\Controllers\IndexController;
use App\Core\Contract\RouteInterface;

class Route implements RouteInterface
{

    public function getResource()
    {
        return [
            '/' => [
                'controller' => IndexController::class, 'method' => 'index', 'args' => []
            ],
            '/api/v1' => [
                'controller' => V1::class, 'method' => 'index'
            ]
        ];
    }
}