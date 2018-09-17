<?php

namespace App\Core\Contract\Controllers;

use App\Core\ServiceRepository;

interface DispatchInterface
{

    public function dispatch(ServiceRepository $services);
}