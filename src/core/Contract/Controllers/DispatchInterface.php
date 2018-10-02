<?php

namespace System\Contract\Controllers;

use System\ServiceRepository;

interface DispatchInterface
{

    public function dispatch(ServiceRepository $services);
}