<?php

namespace System;

use System\Contract\Controllers\DispatchInterface;

abstract class Controller implements DispatchInterface
{
    protected $services;

    public function dispatch(ServiceRepository $services)
    {
        $this->services = $services;
    }
}