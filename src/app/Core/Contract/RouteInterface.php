<?php

namespace App\Core\Contract;

interface RouteInterface
{
    /**
     * @return array
     */
    public function getResource();

    /**
     * @param RequestInterface $request
     * @return array
     */
    public function getControllerResource(RequestInterface $request);
}