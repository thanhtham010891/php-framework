<?php

namespace App\Core;

use App\Core\Contract\Controllers\DispatchInterface;

abstract class Controller implements DispatchInterface
{
    protected $services;

    public function dispatch(ServiceRepository $services)
    {
        $this->services = $services;
    }
}