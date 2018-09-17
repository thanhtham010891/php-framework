<?php

namespace App\Controllers;

use App\Core\Contract\Controllers\DispatchInterface;
use App\Core\ServiceRepository;

abstract class Dispatch implements DispatchInterface
{
    protected $services;

    public function dispatch(ServiceRepository $services)
    {
        $this->services = $services;
    }
}